@extends('adminlte::page')

@section('title','Daftar Barang')
@section('content_header')
    <h1>Daftar Barang</h1>
@stop

@section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-body">
                            <!-- @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
                               <strong>{{  session('success') }}</strong>
                                
                              </div>
                            @endif

                            @if(session('error'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>{{  session('error')}} </strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                            @endif -->

                            <div class="float-right">
                                <a href="{{ route('product.create')}}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Create</a>

                            </div>
                            <table class="table table-striped">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col" width="350px">Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                
                                    @foreach ($product as $c)
                                    <tr>
                                        <th scope="row">{{ ++$i }}</th>
                                        <td>{{ $c->name }}</td>
                                        <td>{{ $c->uom }}</td>
                                        <td>
                                            <div class="d-flex justify-content-around">
                                                <a href="{{ route('product.edit',$c->id)}}" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                    Edit</a>
                                                <form action="{{ route('product.destroy',$c->id)}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                        Delete</button>
                                                </form>
                                            </div>
                                            
                                        </td>
                                      </tr>
                                    @endforeach
                               
                                 
                                </tbody>
                              </table>

                              <!-- PAGINATION -->
                              <div class="d-flex justify-content-between align-items-center mb-2">
    <p><strong>Menampilkan {{ $product->count() }} dari {{ $product->total() }} data</strong></p>
    <div>
        {{ $product->links() }}
    </div>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop

@section('js')
   <script>
       //fungsi dibawah untuk menghilangkan alert dengan efek fadeout   
        $("#success-alert").fadeTo(2000, 500).fadeOut(500, function(){
        $("#success-alert").fadeOut(500);
        });
   </script>

@stop