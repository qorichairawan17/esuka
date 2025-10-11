 <div id="preloader">
     <div id="status">
         <div class="spinner">
             <div class="double-bounce1"></div>
             <div class="double-bounce2"></div>
         </div>
     </div>
 </div>

 <section class="bg-home d-flex align-items-center">
     <div class="container text-center">
         <img src="{{ asset('icons/horizontal-e-suka.png') }}" style="max-width: 250px;" alt="logo">
         <div class="text-uppercase mt-4 display-5 mb-3 fw-semibold">{{ $code }}</div>
         <p class="text-danger fw-bold para-desc mx-auto mb-0">
             "{{ $message }}"
         </p>
         <a href="{{ url()->previous() == url()->current() ? route('app.home') : url()->previous() }}" class="btn btn-primary btn-sm mt-3">Kembali</a>
     </div>
 </section>
