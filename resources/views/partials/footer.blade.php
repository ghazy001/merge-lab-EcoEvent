<div class="container-fluid footer bg-dark text-body py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item">

                </div>
            </div>


            <div class="col-md-6">
                <div class="footer-item">
                    <h4 class="mb-4 text-white">Our Gallery</h4>
                    <div class="row g-3">
                        @foreach ([1,2,3,4,5,6] as $i)
                            <div class="col-4">
                                <div class="footer-gallery position-relative overflow-hidden rounded">
                                    <img src="{{ asset('img/gallery-footer-' . $i . '.jpg') }}" class="img-fluid w-100" alt="Gallery Image {{ $i }}">
                                    <div class="footer-search-icon position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease;">
                                        <a href="{{ asset('img/gallery-footer-' . $i . '.jpg') }}" data-lightbox="footerGallery" data-title="Gallery Image {{ $i }}" class="btn btn-light btn-sm rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid copyright py-4">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-4 text-center text-md-start mb-md-0">
                <span class="text-body"><a href="#" class="text-decoration-none"><i class="fas fa-copyright text-light me-2"></i>EcoEvent</a>, All right reserved.</span>
            </div>
            <div class="col-md-4 text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <a href="https://www.facebook.com/ghazi.saoudi.3" class="btn-hover-color btn-square text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/ghazi_sdi/" class="btn-hover-color btn-square text-white me-2"><i class="fab fa-instagram"></i></a>
                    <a href="https://github.com/ghazy001" class="btn-hover-color btn-square text-white me-2"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/ghazi-saoudi-5b6086271/" class="btn-hover-color btn-square text-white me-0"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-4 text-center text-md-end text-body">
                Designed By <a class="border-bottom text-decoration-none" href="https://github.com/ghazy001">Ghazi Saoudi</a> Distributed By <a class="border-bottom text-decoration-none" href="https://github.com/ghazy001">Ghazi Saoudi</a>
            </div>
        </div>
    </div>
</div>

<style>
    .footer-gallery:hover .footer-search-icon {
        opacity: 1 !important;
    }

    .footer-gallery img {
        transition: transform 0.3s ease;
        aspect-ratio: 1/1;
        object-fit: cover;
    }

    .footer-gallery:hover img {
        transform: scale(1.1);
    }

    .footer-search-icon a {
        transition: all 0.3s ease;
    }

    .footer-search-icon a:hover {
        transform: scale(1.1);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
    }
</style>
