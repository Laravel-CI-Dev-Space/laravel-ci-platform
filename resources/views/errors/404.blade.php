@extends('layouts.web')

@section('title', '404 — Page Not Found · Laravel CI')
@section('description', 'The page you are looking for does not exist.')

@section('content')

<main>
  <section style="min-height: 80vh; display: flex; align-items: center; padding: 4rem 0;">
    <div class="container">
      <div class="row align-items-center g-5 justify-content-center">

        {{-- Left: message --}}
        <div class="col-lg-6 text-center text-lg-start">

          {{-- Error code --}}
          <div class="mb-3" style="position: relative; display: inline-block;">
            <span class="mono" style="
              font-size: clamp(6rem, 18vw, 10rem);
              font-weight: 700;
              line-height: 1;
              color: var(--orange);
              letter-spacing: -.04em;
              display: block;
              text-shadow: 6px 6px 0 var(--navy);
            ">404</span>
          </div>

          <h1 class="mb-3" style="font-size: var(--fs-h2); color: var(--navy);">
            Page Not Found
          </h1>

          <p class="lead mb-4" style="max-width: 28rem;">
            Looks like this route doesn't exist — not even in our
            <code class="mono" style="background: var(--surface); border: 1px solid var(--border); border-radius: .3rem; padding: .1em .4em; font-size: .9em;">routes/web.php</code>.
          </p>

          {{-- CTA buttons --}}
          <div class="d-flex flex-wrap gap-3 mb-5 justify-content-center justify-content-lg-start">
            <a href="{{ route('home') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-house"></i> Back to Home
            </a>
            <a href="{{ route('forum.index') }}" class="btn btn-outline-navy btn-lg">
              <i class="fa-solid fa-comments"></i> Forum
            </a>
          </div>

          {{-- Quick links --}}
          <p class="text-muted-2 mb-2" style="font-size: .88rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 600;">
            You might be looking for
          </p>
          <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start">
            <a href="{{ route('forum.index') }}" class="tag"><i class="fa-solid fa-comments"></i> Forum</a>
            <a href="{{ route('blog.index') }}"  class="tag"><i class="fa-solid fa-newspaper"></i> Blog</a>
            <a href="#" class="tag"><i class="fa-solid fa-calendar-days"></i> Events</a>
            <a href="#"  class="tag"><i class="fa-solid fa-briefcase"></i> Jobs</a>
            <a href="{{ route('about') }}"       class="tag"><i class="fa-solid fa-info-circle"></i> About</a>
          </div>
        </div>

        {{-- Right: IDE window with artsy abort(404) snippet --}}
        <div class="col-lg-5 d-none d-lg-block">
          <div class="ide-window" style="box-shadow: var(--shadow-hard);">
            <div class="ide-bar">
              <span class="ide-dots"><i></i><i></i><i></i></span>
              <span class="ide-tab"><i class="fa-brands fa-php"></i> routes/web.php</span>
            </div>
            <pre class="ide-code" style="min-height: 14rem;"><code
><span class="ln"><span class="c-com">// Hmm, this route was never defined.</span></span
><span class="ln"> </span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">get</span>(<span class="c-str">'{{ request()->path() }}'</span>, <span class="c-key">function</span> () {</span
><span class="ln">    <span class="c-com">// TODO: add this route?</span></span
><span class="ln"> </span
><span class="ln">    <span class="c-key">abort</span>(<span class="c-cls">404</span>);</span
><span class="ln">                   <span class="c-com">^</span></span
><span class="ln">    <span class="c-com">// Already there 😅</span></span
><span class="ln">})->name(<span class="c-str">'mystery'</span>);</span
><span class="ln"> </span
><span class="ln"><span class="c-com">// Meanwhile, these exist:</span></span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">get</span>(<span class="c-str">'/'</span>);<span class="c-com">         // ✓ home</span></span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">prefix</span>(<span class="c-str">'forum'</span>);<span class="c-com">   // ✓ forum</span></span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">prefix</span>(<span class="c-str">'blog'</span>);<span class="c-com">    // ✓ blog</span></span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">prefix</span>(<span class="c-str">'events'</span>);<span class="c-com">  // ✓ events</span></span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">prefix</span>(<span class="c-str">'jobs'</span>);<span class="c-com">    // ✓ jobs</span></span
><span class="ln"><span class="c-key">Route</span>::<span class="c-fn">get</span>(<span class="c-str">'dashboard'</span>);<span class="c-com"> // ✓ yours<span class="caret"></span></span></span
></code></pre>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

@endsection
