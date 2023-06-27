@extends('layouts.presensi')
@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">E-Presensi</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 15px;

    }

    #map {
        height: 200px;
    }
</style>
@endsection
@section('content')
<div class="row" style="margin-top: 70px;">
    <div class="col">
        <input type="hidden" id="lokasi" name="lokasi">
        <div id="webcam-capture" class="webcam-capture">
        </div>

        <div class="row">
            <div class="col">
                @if($cek>0)
                <button id="takeabsen" class="btn btn-danger btn-block"><ion-icon name="camera-reverse-outline"></ion-icon>Absen Pulang</button>
                @else
                <button id="takeabsen" class="btn btn-primary btn-block"><ion-icon name="camera-reverse-outline"></ion-icon>Absen Masuk</button>

                @endif
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
<audio id="notifikasi_in">
    <source src="{{ asset('assets/sounds/notifikasi_in.mp3') }}">
</audio>
<audio id="notifikasi_out">
    <source src="{{ asset('assets/sounds/notifikasi_out.mp3') }}">
</audio>
<audio id="notifikasi_radius">
    <source src="{{ asset('assets/sounds/notifikasi_radius.mp3') }}">
</audio>
@endsection

@push('myscript')
<script language="Javascript">
    var notifikasi_in = document.getElementById('notifikasi_in');
    var notifikasi_out = document.getElementById('notifikasi_out');
    var notifikasi_radius = document.getElementById('notifikasi_radius');
    Webcam.set({
        height: 480,
        width: 640,
        image_format: 'jpeg',
        jpeg_quality: 80
    });
    Webcam.attach('webcam-capture');

    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + ',' + position.coords.longitude;
        var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        var circle = L.circle([-6.315071, 107.127059], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 100
        }).addTo(map);
    }

    function errorCallback() {}
    $('#takeabsen').click(function(e) {
        Webcam.snap(function(uri) {
            image = uri;
        });
        var lokasi = $('#lokasi').val();
        $.ajax({
            type: 'POST',
            url: '/presensi/store',
            data: {
                _token: "{{csrf_token()}}",
                image: image,
                lokasi: lokasi
            },
            cache: false,
            success: function(respond) {
                var status = respond.split("|");
                if (status[0] == "success") {
                    if (status[2] == "in") {
                        notifikasi_in.play();
                    } else {
                        notifikasi_out.play();
                    }

                    swal({
                        title: "Berhasil!",
                        text: status[1],
                        icon: "success",
                        button: "OK !",
                    })
                    setTimeout("location.href='../dashboard'", 3000);
                } else {
                    if (status[0] == "error") {
                        if (status[2] == "radius") {
                            notifikasi_radius.play();
                        }
                    }
                    swal({
                        title: "Gagal!",
                        text: status[1],
                        icon: "error",
                        button: "OK !",
                    })
                    setTimeout("location.href='../dashboard'", 3000);
                }
            }
        });
    });
</script>
@endpush