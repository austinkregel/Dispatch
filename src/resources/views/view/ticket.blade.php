@extends(config('kregel.dispatch.view-base'))
@section('content')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.11/vue.js"></script>
	<div class="container spark-screen">
		<div class="row">
			<div class="col-md-4">
				@include('dispatch::shared.menu')
			</div>
			<li class="col-md-8">
				<ul class="collapsible popout" data-collapsible="accordion">
					@foreach($jurisdictions as $jurisdiction)
						<li class="jurisdiction-wrapper">
							<div class="card collapsible-header" style="background:#<?php
							$name = $jurisdiction->name;
							$char_array = str_split($name);
							$num =ord($name);
							$old_num = $num;
							if($num < 100){
								if(strlen($num) == 2){
									$num = ($num *  11111) - ($num * 100);
								}else{
									$num = $num * 11111;
								}
							} else if( $num < 1000){
								$num = $num.$num;
							} else if(strlen($num) === 4){
								$num = $num . (substr($num, 0, 2));
							} else if( strlen($num) === 5)
								$num = $num . (substr($num, 0, 1));


							//sqrt(((r - r1) * .299)^2 + ((g - g1) * .587)^2 + ((b - b1) * .114)^2)

							echo $num;
							?>">

								<div class="card-content white-text">
									<span class="card-title">
										{{$jurisdiction->name}}
										@if($jurisdiction->tickets->count() > 0)
										<span class="badge left red lighten-4 black-text">{{$jurisdiction->tickets->count()}}</span>
										@endif
									</span>
								</div>
							</div>
							@foreach($jurisdiction->tickets as $ticket)
								<div class="card-wrapper collapsible-body" style="padding:20px;">
									<div class="card" style="background:#<?php
									echo colourBrightness($num, 0.2);

	?>">
										<div class="card-content brown-text lighten-5">
											<span class="card-title">
												{{$ticket->title}}
												<div class="close"  >&times;</div>
											</span>
											<p>{{$ticket->body}}</p>
										</div>
										<div class="card-action">
											<a href="#" class="brown-text lighten-5">View updates</a>
											<a href="#" class="brown-text lighten-5">Post an update</a>
										</div>
									</div>
								</div>
							@endforeach
						</li>
					@endforeach
				</ul>
		</div>
	</div>
@endsection

<?php
function colourBrightness($hex, $percent) {

// Work out if hash given
$hash = '';
if (stristr($hex,'#')) {
	$hex = str_replace('#','',$hex);
	$hash = '#';
}
/// HEX TO RGB
$rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
//// CALCULATE
for ($i=0; $i<3; $i++) {
	// See if brighter or darker
	if ($percent > 0) {
		// Lighter
		$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
	} else {
		// Darker
		$positivePercent = $percent - ($percent*2);
		$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
	}
	// In case rounding up causes us to go to 256
	if ($rgb[$i] > 255) {
		$rgb[$i] = 255;
	}
}
//// RBG to Hex
$hex = '';
for($i=0; $i < 3; $i++) {
	// Convert the decimal digit to hex
	$hexDigit = dechex($rgb[$i]);
	// Add a leading zero if necessary
	if(strlen($hexDigit) == 1) {
		$hexDigit = "0" . $hexDigit;
	}
	// Append to the hex string
	$hex .= $hexDigit;
}
return $hash.$hex;
}
?>