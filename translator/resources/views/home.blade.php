@extends('layout.app')

@section('title', 'Welcome to OneSolution')

@section('content')
<style>
    .welcome-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e3e6f3 100%);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 40px 30px;
        margin-top: 40px;
        margin-bottom: 40px;
    }
    .welcome-logo {
        height: 64px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: #fff;
        padding: 4px;
    }
    .about-owners {
        margin-top: 32px;
    }
    .owner-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px 16px;
        margin: 10px 0;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="welcome-section text-center">
                <img src="{{ asset('images/OneSolution.jpg') }}" alt="OneSolution Logo" class="welcome-logo mb-3">
                <h1 class="mb-3" style="font-weight: 700; color: #dc3545;">Welcome to OneSolution</h1>
                <p class="lead mb-4" style="color: #333;">
                    <b>OneSolution</b> is your all-in-one platform for document and media processing.<br>
                    Seamlessly convert, translate, summarize, and manage your files with a single, user-friendly interface.<br>
                    Our mission is to empower productivity and creativity for everyone.
                </p>
                <div class="row mt-4">
                    <div class="col-md-6 text-start">
                        <h4 class="mb-2" style="color: #198754;">What can you do here?</h4>
                        <ul style="font-size: 1.1rem;">
                            <li>Translate text, PDFs, and videos</li>
                            <li>Convert files between formats (Word, PDF, Images, Audio, etc.)</li>
                            <li>Summarize and check plagiarism</li>
                            <li>Generate QR codes and more!</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-start">
                        <h4 class="mb-2" style="color: #0d6efd;">Why OneSolution?</h4>
                        <ul style="font-size: 1.1rem;">
                            <li>Modern, easy-to-use interface</li>
                            <li>Fast, secure, and reliable</li>
                            <li>All your tools in one place</li>
                            <li>Built with ❤️ by passionate developers</li>
                        </ul>
                    </div>
                </div>
                <div class="about-owners mt-5">
                    <h4 class="mb-3" style="color: #6f42c1;">Meet the Team</h4>
                    <div class="row justify-content-center">
                        <div class="col-md-5 owner-card mx-2 mb-3 d-flex align-items-center">
                            <img src="{{ asset('images/096_abhishek.jpg') }}" alt="Abhishek Pandey" style="height: 64px; width: 64px; object-fit: cover; border-radius: 50%; margin-right: 18px; border: 2px solid #dc3545; background: #fff;">
                            <div class="text-start">
                                <h5 class="mb-1">Abhishek Pandey</h5>
                                <div style="font-size: 0.95rem; color: #555;">Lead Developer & Visionary</div>
                            </div>
                        </div>
                        <div class="col-md-5 owner-card mx-2 mb-3 d-flex align-items-center">
                            <img src="{{ asset('images/029_zeenal.jpg') }}" alt="Zeenal Bhalodiya" style="height: 64px; width: 64px; object-fit: cover; border-radius: 50%; margin-right: 18px; border: 2px solid #0d6efd; background: #fff;">
                            <div class="text-start">
                                <h5 class="mb-1">Zeenal Bhalodiya</h5>
                                <div style="font-size: 0.95rem; color: #555;">Co-Developer & Creative Partner</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3" style="font-size: 1rem; color: #888;">
                        Thank you for choosing OneSolution. We hope you enjoy using our platform!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
