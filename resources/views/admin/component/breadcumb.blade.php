 <div class="d-md-flex justify-content-between align-items-center">
     <h5 class="mb-0">{{ $pageTitle }}</h5>

     <nav aria-label="breadcrumb" class="d-inline-block mt-2 mt-sm-0">
         <ul class="breadcrumb bg-transparent rounded mb-0 p-0">
             @foreach ($breadCumb as $bc)
                 <li class="breadcrumb-item text-capitalize {{ $bc['active'] }}">
                     <a href="{{ $bc['url'] }}" {{ $bc['aria'] }}>{{ $bc['title'] }}</a>
                 </li>
             @endforeach
         </ul>
     </nav>
 </div>
