<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GoyzerService
{
    protected string $baseUrl;

    protected string $accessCode;

    protected string $groupCode;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('goyzer.base_url'), '/');
        $this->accessCode = (string) config('goyzer.access_code');
        $this->groupCode = (string) config('goyzer.group_code');
    }

    public function fetchSalesListings(): array
    {
        $params = [
            'AccessCode' => $this->accessCode,
            'GroupCode' => $this->groupCode,
            'Bedrooms' => '',
            'StartPriceRange' => '',
            'EndPriceRange' => '',
            'CategoryID' => '',
            'StatePK' => '',
            'CommunityPK' => '',
            'UnitPK' => '',
            'PropertyPK' => '',
            'SpecialProjects' => '',
            'CountryID' => '',
            'StateID' => '',
            'CommunityID' => '',
            'DistrictID' => '',
            'FloorAreaMin' => '',
            'FloorAreaMax' => '',
            'UnitCategory' => '',
            'UnitID' => '',
            'BedroomsMax' => '',
            'PropertyID' => '',
            'ReadyNow' => '',
            'PageIndex' => '',
        ];

        $xml = $this->sendGetRequest('SalesListings', $params);

        return $this->parseListingsXml($xml);
    }

    public function fetchRentListings(): array
    {
        $params = [
            'AccessCode' => $this->accessCode,
            'GroupCode' => $this->groupCode,
            'UnitPK' => '',
            'PropertyType' => '',
            'Bedrooms' => '',
            'StatePK' => '',
            'CommunityPK' => '',
            'StartPriceRange' => '',
            'EndPriceRange' => '',
            'SpecialProjects' => '',
            'CategoryID' => '',
            'StatePK' => '',
            'CommunityPK' => '',
            'UnitPK' => '',
            'PropertyPK' => '',
            'SpecialProjects' => '',
            'CountryID' => '',
            'StateID' => '',
            'CommunityID' => '',
            'DistrictID' => '',
            'FloorAreaMin' => '',
            'FloorAreaMax' => '',
            'UnitCategory' => '',
            'UnitID' => '',
            'BedroomsMax' => '',
            'PropertyID' => '',
            'ReadyNow' => '',
            'PageIndex' => '',
        ];

        $xml = $this->sendGetRequest('RentListings', $params);

        return $this->parseListingsXml($xml);
    }

    protected function sendGetRequest(string $method, array $params): string
    {
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $url = "{$this->baseUrl}/{$method}?{$query}";

        $response = Http::timeout(60)
            ->withHeaders([
                'Accept' => 'application/xml, text/xml, */*',
            ])
            ->get($url);

        $response->throw();

        return $response->body();
    }

    protected function parseListingsXml(string $xml): array
    {
        $xml = trim($xml);

        if ($xml === '') {
            return [];
        }

        libxml_use_internal_errors(true);
        $root = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (! $root) {
            $errors = libxml_get_errors();
            libxml_clear_errors();

            throw new \RuntimeException('Failed to parse Goyzer XML.');
        }

        $json = json_encode($root);
        $data = json_decode($json, true);

        return $this->extractRows($data);
    }

    protected function extractRows(array $data): array
    {
        $candidateKeys = [
            'Unit',
            'Units',
            'Table',
            'Tables',
            'Property',
            'Properties',
            'Listing',
            'Listings',
            'SalesListings',
            'RentsListings',
            'SalesListing',
            'RentListing',
            'RentListings',
        ];

        foreach ($candidateKeys as $key) {
            if (isset($data[$key])) {
                return $this->normalizeRows($data[$key]);
            }
        }

        if ($this->looksLikeSingleListing($data)) {
            return [$this->normalizeKeys($data)];
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                $rows = $this->normalizeRows($value);
                if (! empty($rows)) {
                    return $rows;
                }
            }
        }

        return [];
    }

    protected function normalizeRows(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        if ($this->looksLikeSingleListing($rows)) {
            return [$this->normalizeKeys($rows)];
        }

        $normalized = [];

        foreach ($rows as $row) {
            if (is_array($row)) {
                $normalized[] = $this->normalizeKeys($row);
            }
        }

        return $normalized;
    }

    protected function looksLikeSingleListing(array $row): bool
    {
        $keys = array_map('strtolower', array_keys($row));

        $signals = [
            'unit_pk',
            'unitpk',
            'unit_reference_no',
            'property_name',
            'marketing_title',
            'selling_price',
            'rent_per_annum',
        ];

        foreach ($signals as $signal) {
            if (in_array($signal, $keys, true)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeKeys(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $newKey = \Illuminate\Support\Str::snake((string) $key);

            if (is_array($value)) {
                $normalized[$newKey] = $this->normalizeKeysRecursive($value);
            } else {
                $normalized[$newKey] = $value;
            }
        }

        return $normalized;
    }

    protected function normalizeKeysRecursive(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $newKey = is_string($key) ? \Illuminate\Support\Str::snake($key) : $key;
            $normalized[$newKey] = is_array($value) ? $this->normalizeKeysRecursive($value) : $value;
        }

        return $normalized;
    }

    public function mapToUnitData(array $row, string $type): array
    {
        $code = $this->scalarValue($row['code'] ?? null);
        $imageRows = $this->extractImageRows($row, $code);
        $coordinates = $this->extractCoordinates(
            $this->scalarValue($row['pro_googlecoordinates'] ?? $row['pro_google_coordinates'] ?? null)
        );

        $code = $this->scalarValue($row['code'] ?? null);
        $refNo = $this->scalarValue($row['ref_no'] ?? $row['refno'] ?? null);
        $propertyName = $this->scalarValue($row['property_name'] ?? null);
        $marketingTitle = $this->scalarValue($row['marketing_title'] ?? null);
        $remarks = html_entity_decode((string) $this->scalarValue($row['remarks'] ?? null));

        $sellingPrice = $this->toDecimal($row['sell_price'] ?? $row['selling_price'] ?? null);

        $rentPrice = $this->toDecimal(
            $row['rent_per_month'] ?? $row['rent_price'] ?? $row['rent_per_annum'] ?? null
        );
        $countryName = $this->scalarValue($row['country_name'] ?? null);

        return [
            'unit_for' => $type === 'sale' ? 'For Sale' : 'For Rent',
            'listing_type' => $type,
            'is_for_sale' => $type === 'sale' ? 1 : 0,
            'is_for_rent' => $type === 'rent' ? 1 : 0,

            'code' => $code,
            'ref_no' => $refNo,
            'property_name' => $propertyName,
            'marketing_title' => $marketingTitle,
            'meta_title' => $marketingTitle ?: $propertyName,
            'remarks' => $remarks,

            'category' => $this->scalarValue($row['category'] ?? null),
            'status' => $this->scalarValue($row['status'] ?? '1'),
            'is_active' => 1,

            'builtup_area' => $this->toDecimal($row['builtup_area'] ?? null),
            'plot_area' => $this->toDecimal($row['plot_area'] ?? null),
            'bedrooms' => $this->toInt($row['bedrooms'] ?? null),
            'no_of_bathrooms' => $this->toInt($row['no_of_bathrooms'] ?? null),
            'floor_no' => $this->toInt($row['floor_no'] ?? null),
            'parking' => $this->toInt($row['parking'] ?? null),

            'primary_unit_view' => $this->scalarValue($row['primary_unit_view'] ?? null),
            'primary_unit_view_id' => $this->scalarValue($row['primary_unit_view_id'] ?? null),
            'secondary_unit_view' => $this->scalarValue($row['secondary_unit_view'] ?? null),
            'secondary_unit_view_id' => $this->scalarValue($row['secondary_unit_view_id'] ?? null),

            'unit_model' => $this->scalarValue($row['unit_model'] ?? null),
            'sub_type' => $this->scalarValue($row['sub_type'] ?? null),

            'country_name' => $countryName,
            'country_id' => 2, // $this->resolveCountryId($countryName),
            'data_source' => 'goyzer',
            'state_name' => $this->scalarValue($row['state_name'] ?? null),
            'city_name' => $this->scalarValue($row['city_name'] ?? null),
            'district_name' => $this->scalarValue($row['district_name'] ?? null),
            'community' => $this->scalarValue($row['community'] ?? null),
            'sub_community' => $this->scalarValue($row['sub_community'] ?? null),

            'marketing_usp' => $this->scalarValue($row['marketing_usp'] ?? null),
            'financing_company' => $this->scalarValue($row['financing_company'] ?? null),
            'property_overview' => $this->scalarValue($row['property_overview'] ?? null),
            'local_area_amenities_desc' => $this->scalarValue($row['local_area_amenities_desc'] ?? null),
            'document_web' => $this->scalarValue($row['document_web'] ?? null),

            'sell_price' => $sellingPrice,
            'selling_price' => $sellingPrice,
            'rent_price' => $rentPrice,

            'ret_unit_category' => $this->toInt($row['ret_unit_category'] ?? null),
            'pdf_brochure_link' => $this->scalarValue($row['pdf_brochure_link'] ?? null),
            'agent_id' => $this->toInt($row['agent_id'] ?? null),
            'property_id' => $this->toInt($row['property_id'] ?? null),

            'property_ownership_desc' => $this->scalarValue($row['property_ownership_desc'] ?? null),

            'state_id' => $this->toInt($row['state_id'] ?? null),
            'city_id' => $this->toInt($row['city_id'] ?? null),
            'district_id' => $this->toInt($row['district_id'] ?? null),
            'community_id' => $this->toInt($row['community_id'] ?? null),
            'sub_community_id' => $this->toInt($row['sub_community_id'] ?? null),

            'agent_rera_no' => $this->scalarValue($row['agent_rera_no'] ?? null),
            'pro_google_coordinates' => $this->scalarValue($row['pro_googlecoordinates'] ?? $row['pro_google_coordinates'] ?? null),
            'bdm_pkg' => $this->scalarValue($row['bdm_pkg'] ?? null),
            'salesman_email' => $this->scalarValue($row['salesman_email'] ?? null),

            'last_updated' => $this->toDateTime($row['last_updated'] ?? null),
            'listing_date' => $this->toDateTime($row['listing_date'] ?? null),
            'expiry_date' => $this->toDate($row['expiry_date'] ?? null),

            'recommended_properties' => $this->scalarValue($row['recommended_properties'] ?? null),
            'marketing_options' => $this->scalarValue($row['marketing_options'] ?? null),
            'agent_photo' => $this->scalarValue($row['agent_photo'] ?? null),
            'arabic_title' => $this->scalarValue($row['arabic_title'] ?? null),
            'arabic_description' => $this->scalarValue($row['arabic_description'] ?? null),
            'mandate' => $this->scalarValue($row['mandate'] ?? null),
            'currency_abr' => $this->scalarValue($row['currency_abr'] ?? null),
            'area_measurement' => $this->scalarValue($row['area_measurement'] ?? null),
            'rera_str_no' => $this->scalarValue($row['rera_str_no'] ?? null),
            'furnish_status' => $this->scalarValue($row['furnish_status'] ?? null),
            'listing_type' => $this->scalarValue($row['listing_type'] ?? $type),
            'permit_number' => $this->scalarValue($row['permit_number'] ?? null),

            'agent' => $this->scalarValue($row['agent'] ?? null),
            'contact_number' => $this->scalarValue($row['contact_number'] ?? null),

            'thumbnail' => $imageRows[0]['image_url'] ?? null,
            'images' => null,

            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],

            'slug' => \Illuminate\Support\Str::slug($marketingTitle ?: $propertyName ?: ('unit-'.$code)),
            'is_auto_slug' => 1,
        ];
    }

    public function extractFittings(array $row, ?string $code = null): array
    {
        $results = [];

        if (! isset($row['fitting_fixtures']['fitting_fixture'])) {
            return $results;
        }

        $items = $row['fitting_fixtures']['fitting_fixture'];

        // Handle single item
        if (isset($items['name'])) {
            $items = [$items];
        }

        foreach ($items as $item) {
            $results[] = [
                'source' => 'goyzer',
                'source_fixture_id' => $this->scalarValue($item['i_d'] ?? null),
                'title' => $this->scalarValue($item['title'] ?? null),
                'name' => $this->scalarValue($item['name'] ?? null),
            ];
        }

        return $results;
    }

    public function extractImageRows(array $row, ?string $sourceUnitCode = null): array
    {
        $results = [];

        if (! isset($row['images']['image'])) {
            return $results;
        }

        $items = $row['images']['image'];

        if (isset($items['image_u_r_l']) || isset($items['imageurl'])) {
            $items = [$items];
        }

        foreach ($items as $index => $item) {
            $url = $this->scalarValue($item['image_u_r_l'] ?? $item['imageurl'] ?? null);

            if (! $url) {
                continue;
            }

            $results[] = [
                'source' => 'goyzer',
                'source_unit_code' => $sourceUnitCode,
                'image_type' => $this->scalarValue($item['image_type'] ?? null),
                'title' => $this->scalarValue($item['title'] ?? null),
                'is_marketing_image' => strtolower((string) $this->scalarValue($item['is_marketing_image'] ?? 'no')) === 'yes',
                'image_remarks' => $this->scalarValue($item['image_remarks'] ?? null),
                'image_url' => html_entity_decode($url),
                'sort_order' => $index,
            ];
        }

        return $results;
    }

    protected function extractCoordinates(?string $coordinates): array
    {
        if (! $coordinates) {
            return ['latitude' => null, 'longitude' => null];
        }

        $parts = preg_split('/[,\s]+/', trim($coordinates));

        return [
            'latitude' => isset($parts[0]) && is_numeric($parts[0]) ? (float) $parts[0] : null,
            'longitude' => isset($parts[1]) && is_numeric($parts[1]) ? (float) $parts[1] : null,
        ];
    }

    protected function toDecimal(mixed $value): ?float
    {
        $value = $this->scalarValue($value);

        if ($value === null || $value === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return $clean === '' ? null : (float) $clean;
    }

    protected function toInt(mixed $value): ?int
    {
        $value = $this->scalarValue($value);

        if ($value === null || $value === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9\-]/', '', (string) $value);

        return $clean === '' ? null : (int) $clean;
    }

    protected function scalarValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        array_walk_recursive($value, function ($item) use (&$flat) {
            if (! isset($flat) && ! is_array($item) && ! is_object($item)) {
                $flat = $item;
            }
        });

        return $flat ?? null;
    }

    protected function toDateTime(mixed $value): ?string
    {
        $value = $this->scalarValue($value);

        if (! $value) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function toDate(mixed $value): ?string
    {
        $value = $this->scalarValue($value);

        if (! $value) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolveCountryId(?string $countryName): ?int
    {
        if (! $countryName) {
            return null;
        }

        $countryName = html_entity_decode($countryName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $countryName = str_replace("\xc2\xa0", ' ', $countryName);
        $countryName = preg_replace('/\s+/u', ' ', $countryName);
        $countryName = trim(mb_strtolower($countryName));

        $aliases = [
            'uae' => 'united arab emirates',
            'united arab emirates' => 'united arab emirates',
            'india' => 'india',
        ];

        $lookup = $aliases[$countryName] ?? $countryName;

        $country = DB::table('countries')->get()->first(function ($item) use ($lookup) {
            $dbName = html_entity_decode($item->country_name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $dbName = str_replace("\xc2\xa0", ' ', $dbName);
            $dbName = preg_replace('/\s+/u', ' ', $dbName);
            $dbName = trim(mb_strtolower($dbName));

            return $dbName === $lookup;
        });

        return $country?->id ? (int) $country->id : null;
    }

    protected function normalizeCountryName(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = $this->scalarValue($value);

        if (! is_string($value)) {
            return null;
        }

        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace("\xc2\xa0", ' ', $value); // non-breaking space
        $value = preg_replace('/\s+/u', ' ', $value);
        $value = trim(mb_strtolower($value));

        return $value !== '' ? $value : null;
    }
}
