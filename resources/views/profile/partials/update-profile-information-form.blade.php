<section>
    <p class="text-muted mb-4">Update your account's profile information and email address.</p>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="mb-4 text-center">
            <div class="mb-3" id="photoPreviewContainer">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle" id="photoPreview" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #e9ecef;">
                @else
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" id="photoPreview" style="width: 100px; height: 100px; font-size: 3rem; border: 3px solid #e9ecef;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                @endif
            </div>
            <div>
                <input type="file" class="form-control d-inline-block w-auto" id="profile_photo" name="profile_photo" accept="image/*" onchange="previewPhoto(event)">
                @if($user->profile_photo_path)
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removePhoto()">Remove Photo</button>
                    <input type="hidden" name="remove_photo" id="remove_photo" value="0">
                @endif
            </div>
            @error('profile_photo')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <small>
                        Your email address is unverified.
                        <form method="post" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 align-baseline text-decoration-underline">
                                Click here to re-send the verification email.
                            </button>
                        </form>
                    </small>

                    @if (session('status') === 'verification-link-sent')
                        <div class="text-success small mt-1">
                            A new verification link has been sent to your email address.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input id="phone_number" name="phone_number" type="text" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required autocomplete="tel">
            @error('phone_number')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small">
                    <i class="bi bi-check-circle me-1"></i> Saved!
                </span>
            @endif
        </div>
    </form>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            console.log('File selected:', file);
            
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('Image loaded, updating preview');
                    const container = document.getElementById('photoPreviewContainer');
                    
                    // Create new image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    img.id = 'photoPreview';
                    img.className = 'rounded-circle';
                    img.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border: 3px solid #e9ecef;';
                    
                    // Replace existing preview
                    container.innerHTML = '';
                    container.appendChild(img);
                    console.log('Preview updated successfully');
                }
                reader.onerror = function(error) {
                    console.error('Error reading file:', error);
                    alert('Error loading image. Please try again.');
                }
                reader.readAsDataURL(file);
            }
        }

        function removePhoto() {
            if (confirm('Are you sure you want to remove your profile photo?')) {
                document.getElementById('remove_photo').value = '1';
                document.querySelector('form').submit();
            }
        }
    </script>
</section>
