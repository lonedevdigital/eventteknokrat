  @if (session()->has('msg'))
      <div class="alert {{ session('class') }} alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h5><i class="icon fas {{ session('icon') }}"></i> Alert!</h5>
          {{ session('msg') }}
      </div>
  @endif
