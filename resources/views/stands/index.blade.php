<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Select Stand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:16px; }
        .card { border:1px solid #ddd; border-radius:10px; padding:12px; }
        .card img { width:100%; height:auto; border-radius:8px; }
        .actions { margin-top:16px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
        .error { color:#b00020; }
        .result { margin-top:20px; }
        .result img { max-width:520px; width:100%; border-radius:12px; border:1px solid #ddd; }
    </style>
</head>
<body>
    <h2>Select a Stand Design</h2>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('stands.generate') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid">
            @foreach($stands as $stand)
                <label class="card">
                    <input type="radio" name="stand_id" value="{{ $stand['id'] }}"
                           {{ old('stand_id') == $stand['id'] ? 'checked' : '' }}>
                    <div style="margin-top:10px;">
                        <strong>{{ $stand['name'] }}</strong>
                    </div>
                    <div style="margin-top:10px;">
                        <img src="{{ asset('stands/'.basename($stand['template'])) }}" alt="{{ $stand['name'] }}">
                    </div>
                </label>
            @endforeach
        </div>

        <div class="actions">
            <div>
                <label><strong>Upload Logo (PNG preferred)</strong></label><br>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" required>
            </div>
            <button type="submit">Generate Mockup</button>
        </div>
    </form>

    @if(!empty($mockupUrl))
        <div class="result">
            <h3>Generated Mockup</h3>
            <img src="{{ asset($mockupUrl) }}" alt="Mockup result">
        </div>
    @endif
</body>
</html>