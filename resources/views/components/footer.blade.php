<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-2">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        Lost & Found
                    </div>
                </h5>
                <p class="text-muted">
                    Helping reunite people with their lost belongings through the power of community and technology.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <h6 class="mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="mb-2"><a href="{{ route('lost-items.index') }}" class="text-muted text-decoration-none">Lost Items</a></li>
                    <li class="mb-2"><a href="{{ route('found-items.index') }}" class="text-muted text-decoration-none">Found Items</a></li>
                    <li class="mb-2"><a href="{{ route('matches.index') }}" class="text-muted text-decoration-none">Matches</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <h6 class="mb-3">Support</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Safety Tips</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Report Issue</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <h6 class="mb-3">Legal</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Cookie Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Guidelines</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2">
                <h6 class="mb-3">Contact</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        support@lostandfound.com
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        (555) 123-4567
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-geo-alt me-2"></i>
                        New York, NY
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-muted mb-0">
                    &copy; {{ date('Y') }} Lost & Found. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-muted mb-0">
                    Made with <i class="bi bi-heart-fill text-danger"></i> for the community
                </p>
            </div>
        </div>
    </div>
</footer>
