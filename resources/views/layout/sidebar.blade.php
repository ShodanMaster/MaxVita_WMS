<nav class="sidebar">

	<div class="sidebar-header">

		<a href="{{route('dashboard')}}" class="sidebar-brand">

			{{-- Stallion<span>  WMS</span> --}}
			{{-- {{dd($config->front_logo);}} --}}
			@if(isset($anotherArray->front_logo))

			<img src='{{ asset("dist/img/$anotherArray->front_logo") }}' alt="AdminLTE Docs Logo Large" class="brand-image-xs logo-xl" style="width: 200px">
			@else
			Stallion<span> WMS</span>
			@endif
		</a>

		<div class="sidebar-toggler not-active">

			<span></span>

			<span></span>

			<span></span>

		</div>

	</div>

	<div class="sidebar-body">

		<ul class="nav">




			@foreach ($menus as $menu)
			<!-- @if ($menu['id'] == 2)
			<li class="nav-item nav-category">Mater</li>
			@endif -->

			@if ($menu['id'] == 3)
			<li class="nav-item nav-category">Transactions</li>
			@endif

			@if ($menu['id']==5)
			<li class="nav-item nav-category">Report</li>
			@endif


			@if ($menu['id']==6)
			<li class="nav-item nav-category">Settings</li>
			@endif
			@if(count($menu['submenus']))



			<li class="nav-item @if($menu['id']==$activemenu[0]) active @endif">
				@if($menu['id']==1)
				<a class="nav-link" data-toggle="collapse" href="{{url('/')}}" role="button" aria-expanded="@if($menu['id']==$activemenu[0]) true @else false @endif">

					<i class="link-icon" data-feather="{{ $menu['icon'] }}"></i>

					<span id="mySpan" class="link-title">{{ $menu['title'] }}</span>

				</a>
				@endif

				@if($menu['id']!=1)
				<a class="nav-link" data-toggle="collapse" href="#c{{ $menu['id'] }}" role="button" aria-expanded="@if($menu['id']==$activemenu[0]) true @else false @endif" aria-controls="c{{ $menu['id'] }}">

					<i class="link-icon" data-feather="{{ $menu['icon'] }}"></i>

					<span class="link-title">{{ $menu['title'] }}</span>


					<i class="link-arrow" data-feather="chevron-down"></i>


				</a>
				{{-- @endif --}}
				{{-- @if($menu['id']!=1) --}}

				<div class="collapse @if($menu['id']==$activemenu[0]) show @endif" id="c{{ $menu['id'] }}">

					<ul class="nav sub-menu">

						@foreach ($menu['submenus'] as $submenu)

						<li class="nav-item">

							<a href="{{url($submenu->link)}}" class="nav-link @if($submenu->id==$activemenu[1]) active @endif">{{ $submenu->title }}</a>

						</li>

						@endforeach



					</ul>

				</div>

				@endif

			</li>

			@else



			<li class="nav-item @if($menu['id']==$activemenu[0]) active @endif">

				<a href="{{ url($menu['link']) }}" class="nav-link">

					<i class="link-icon" data-feather="box"></i>

					<span class="link-title">{{ $menu['title'] }}</span>

				</a>

			</li>

			@endif

			@endforeach

		</ul>

	</div>

</nav>

<script>
	document.getElementById("mySpan").addEventListener("click", function() {
		window.location.href = "/";
	});
</script>
