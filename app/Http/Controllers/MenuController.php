<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the 2-column Drag & Drop Menu Management page.
     */
    public function index($editId = null)
    {
        $editData = null;
        if ($editId) {
            try {
                $decryptedId = decrypt($editId);
                $editData = Menu::find($decryptedId);
            } catch (\Exception $e) {
                $editData = Menu::find($editId);
            }
        }

        // Fetch all active and inactive menus ordered by order/weight
        $allMenus = Menu::orderBy('order', 'asc')->get();

        // Build nested tree structure
        $menuTree = $this->buildTree($allMenus);

        // Pre-defined available routes in system
        $availableRoutes = [
            'buy.properties'   => 'Buy Properties (/buy)',
            'rent.properties'  => 'Rent Properties (/rent)',
            'project'          => 'Projects (/project-lists)',
            'developers'       => 'Developers (/developers)',
            'services'         => 'Services (/services)',
            'about-us'         => 'About Us (/about-us)',
            'news'             => 'News / Blogs (/news)',
            'contact-us'       => 'Contact Us (/contact-us)',
            'privacy-policy'   => 'Privacy Policy (/privacy-policy)',
            'terms-conditions' => 'Terms & Conditions (/terms-conditions)',
        ];

        return view('org.menus.index', compact('menuTree', 'editData', 'availableRoutes'));
    }

    /**
     * Show form for editing specific menu item on the same 2-column layout.
     */
    public function edit($id)
    {
        return $this->index($id);
    }

    /**
     * Store new or update existing menu item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'url'          => 'nullable|string|max:255',
            'route_name'   => 'nullable|string|max:255',
            'status'       => 'required|in:active,inactive,1,0',
        ]);

        $id = $request->input('id');

        $isExternal = $request->has('ext_url') ? 1 : 0;
        $openNewTab = $request->has('open_new_tab') ? '_blank' : '_self';
        $statusValue = in_array($request->status, ['active', '1', 1]) ? 1 : 0;

        $url = $request->input('url') ?: '#';
        if ($isExternal && $request->filled('ext_url_link')) {
            $url = $request->input('ext_url_link');
        }

        $menuData = [
            'title'      => trim($request->title),
            'url'        => trim($url),
            'route_name' => $request->filled('route_name') ? trim($request->route_name) : null,
            'is_active'  => $statusValue,
            'target'     => $openNewTab,
        ];

        if ($id) {
            $menu = Menu::findOrFail($id);
            $menu->update($menuData);
            $msg = 'Menu item updated successfully!';
        } else {
            // New item order at end
            $maxOrder = Menu::max('order') ?: 0;
            $menuData['order'] = $maxOrder + 1;
            $menuData['parent_id'] = null;
            Menu::create($menuData);
            $msg = 'Menu item added successfully!';
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => $msg]);
        }

        return redirect()->route('org.menus.index')->with('success', $msg);
    }

    /**
     * Update order and nested parent-child hierarchy via AJAX (Drag & Drop).
     */
    public function saveOrder(Request $request)
    {
        $order = $request->input('order');

        if (!empty($order) && is_array($order)) {
            $weight = 1;
            $this->saveNestedHierarchy($order, null, $weight);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Menu order and hierarchy updated successfully!'
        ]);
    }

    /**
     * Recursive helper to save nested hierarchy from Drag & Drop array.
     */
    private function saveNestedHierarchy($items, $parentId = null, &$weight = 1)
    {
        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            if ($id) {
                Menu::where('id', $id)->update([
                    'parent_id' => $parentId,
                    'order'     => $weight++,
                ]);

                if (!empty($item['children']) && is_array($item['children'])) {
                    $this->saveNestedHierarchy($item['children'], $id, $weight);
                }
            }
        }
    }

    /**
     * Delete menu item and its children.
     */
    public function destroy($id)
    {
        try {
            $menuId = decrypt($id);
        } catch (\Exception $e) {
            $menuId = $id;
        }

        $menu = Menu::find($menuId);

        if (!$menu) {
            return redirect()->back()->with('error', 'Menu item not found.');
        }

        $menu->delete();

        return redirect()->route('org.menus.index')->with('success', 'Menu item deleted successfully!');
    }

    /**
     * Helper to build nested tree array from flat collection.
     */
    private function buildTree($menus, $parentId = null)
    {
        $branch = [];
        foreach ($menus as $menu) {
            if ($menu->parent_id == $parentId) {
                $children = $this->buildTree($menus, $menu->id);
                $menu->children_tree = $children;
                $branch[] = $menu;
            }
        }
        return $branch;
    }
}
