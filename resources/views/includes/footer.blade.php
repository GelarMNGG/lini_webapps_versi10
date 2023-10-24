<footer id="contact-us" class='footer row' style="background-color:#1e2554; color:#dddddd;">  
    <div class="container">
        <div class="row pt-5">
            <div class="col-md-8 mb-5"> 
                <span class="text-uppercase text-secondary">Tentang {{ ucfirst($companyInfo->name) }}</span>
                <hr class="hr-footer">
                <p class="lead">
                    {{ $companyInfo->brief }}
                </p>
                <hr class="hr-footer">
                <ul class="social-media c-white">
                    <li><a href="https://www.linkedin.com/company/pt-lima-inti-sinergi"><i class="fab fa-linkedin big"></i> Linkedin</a></li>
                </ul>
            </div>
            <div class="col-md-4"> 
                <span class="text-uppercase text-secondary">Kontak kami</span>
                <hr class="hr-footer">
                <ul class="footer-ul c-white">
                    <li><a href='https://wa.me/{{ $companyInfo->mobile }}?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white mb-1'> <i class='fab fa-whatsapp' title='Whatsapp'></i> {{ $companyInfo->mobile }}</a></li>
                    <li><a href='https://wa.me/628119279972?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white'> <i class='fab fa-whatsapp' title='Whatsapp'></i> 628119279972</a></li>
                    <li><a href="#"><i class="fa fa-phone"></i> {{ $companyInfo->phone }}</a></li>
                    <li><a href="#"><i class="fa fa-globe-asia"></i> {{ $companyInfo->url }}</a></li>
                    <li><a href="#"><i class="fa fa-map-marker-alt"></i> {!! $companyInfo->address !!}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="map">
        <iframe src="{{ $companyInfo->map }}" width="100%" height="400" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
    <div class="footer-bottom-list">
        <div class="container text-center pdt-11 pdb-21 text-orange">
            <span>Copyright &copy; {{ ucfirst($companyInfo->url) }}</span>
        </div>
    </div>
</footer>
<!-- jQuery -->
<script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<!-- Others -->