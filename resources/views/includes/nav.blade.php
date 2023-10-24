<nav class="navbar smart-scroll navbar-expand-lg navbar-dark">

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse text-uppercase" id="main_nav">
    <ul class="navbar-nav">
      <li class="nav-item active"> <a class="nav-link" href="{{ route('home') }}">Home </a> </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          About
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <h6 class="dropdown-header">Informasi LINI</h6>
          <a class="dropdown-item" href="{{ route('about') }}">Tentang perusahaan</a>
          <a class="dropdown-item" href="{{ route('company-history') }}">Sejarah LINI</a>
          <a class="dropdown-item" href="{{ route('corporate-culture') }}">Budaya perusahaan</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Our services
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <h6 class="dropdown-header">Layanan LINI</h6>
          @if(sizeof($navServices) > 0)
            @foreach($navServices as $navService)
              <a class="dropdown-item" href="{{ route('our-services', $navService->slug) }}">{{ strtoupper($navService->name) }}</a>
            @endforeach
          @endif
        </div>
      </li>
      
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Update
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <h6 class="dropdown-header">LINI Update</h6>
          <a class="dropdown-item" href="{{ route('blog') }}">Berita/Blog</a>
          <a class="dropdown-item dropdown" href="#">LINI Peduli</a>
        </div>
      </li>

      <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#contact-us"> Contact </a></li>
    </ul>
  </div> <!-- navbar-collapse.// -->
</nav>