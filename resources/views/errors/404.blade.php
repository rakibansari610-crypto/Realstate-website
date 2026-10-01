@extends('layouts.frontend.frontend_layout')

@section('main-content')
<section class="error-page-v2">
    <div class="container">
        <div class="error-box">
            <div class="error-icon">🚫</div>
            <div class="error-code">404</div>
            <h2>Sorry, we can’t find that page</h2>
            <p>
                It seems the page you were trying to reach doesn’t exist anymore
                or the URL may be incorrect.
            </p>

            <div class="error-btns">
                @php
$country = request()->route('country')
        ?? request()->segment(1)
        ?? session('country')
        ?? session('country_code')
        ?? 'in';
@endphp

<a href="{{ url($country) }}" class="primary-btn">Back Home</a>
<a href="{{ url()->previous() ?? url($country) }}" class="secondary-btn">Previous Page</a>
            </div>
        </div>
    </div>
</section>

<style>
    .error-page-v2 {
        min-height: 75vh;
        display: flex;
        align-items: center;
        background: #f9fbfd;
        padding: 80px 0;
    }

    .error-box {
        max-width: 650px;
        margin: auto;
        text-align: center;
        padding: 50px 30px;
    }

    .error-icon {
        font-size: 60px;
        margin-bottom: 15px;
    }

    .error-code {
        font-size: 110px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 10px;
    }

    .error-box h2 {
        font-size: 34px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
    }

    .error-box p {
        font-size: 16px;
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .error-btns {
        display: flex;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .primary-btn,
    .secondary-btn {
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
    }

    .primary-btn {
        background: #2563eb;
        color: #fff;
    }

    .primary-btn:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .secondary-btn {
        background: transparent;
        color: #2563eb;
        border: 1px solid #2563eb;
    }

    .secondary-btn:hover {
        background: #2563eb;
        color: #fff;
    }
</style>
@endsection