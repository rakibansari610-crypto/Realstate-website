@foreach ($news as $blog)
                    <div class="box col-lg-4 col-md-6 mb-5">
                        <a href="{{ route('news-details', $blog->slug) }}" class="flat-blog-item hover-img">

                            <div class="img-style blog-img-box">
                                @php
                                    $defaultImage = 'assets/images/defalt/dummy_property_images.png';

                                    if (!empty($blog->featured_image) && Storage::disk('public')->exists($blog->featured_image)) {
                                        $Newsimg = asset('storage/'.$blog->featured_image);
                                    } else {
                                        $Newsimg = asset($defaultImage);
                                    }
                                @endphp

                                <img src="{{ $Newsimg }}" alt="img-blog">

                                <span class="date-post">
                                    {{ $blog->created_at->format('F d, Y') }}
                                </span>
                            </div>
                            <div class="content-box">
                                <div class="post-author">
                                    <span>{{ ucwords($blog->category ?? '') ?: "\u{00A0}" }}</span>
                                </div>

                                <h6 class="title two-line">{{ ucwords($blog->title) }}</h6>

                                <p class="description two-line">
                                    {{ $blog->short_description }}
                                </p>
                            </div>

                        </a>
                    </div>
                @endforeach