
<style>
			table.report1{
			font-family: "Times new Roman";
			border-collapse: collapse;
			font-size: 0.9em;
			}

			.report1 th,.report1 td{
			padding: 3px;
			}
			.subh2{
			font-size: 0.9em;
			white-space: nowrap;
			}
			.left{ text-align: left; }
			.right{ text-align: right; }

			h3,h2{
			line-height: 100%;
			}
			p {
			line-height: 0.8em;
			}
		</style>



        <div >
        @foreach ($grn->barcodes as $barcode )

			<table  class="report1" >
				<thead>


				</thead>

				<tbody>
                        <tr>
                            <td>
                                {{-- {!! QrCode::size(200)->generate($pr->serial_no); !!} --}}
                                {{ $barcode->serial_number }}
                            </td>
                        </tr>
                        <tr>
                           <td>
                           {{$barcode->grn_no}}<br>
                           {{$barcode->grn->grn_number}}<br>
                            {{$barcode->item->name}}
                           </td>
                        </tr>


				</tbody>
			</table>
            <br>
                        <br>

            @endforeach
        </div>




<script>
    window.print();
    window.onafterprint = function(){
        document.location.href="{!! route('grn.index') !!}";
    }
</script>
