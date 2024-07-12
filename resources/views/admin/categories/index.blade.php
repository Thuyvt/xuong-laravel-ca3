@extends('admin.layouts.master')

@section('title')
    Danh sách danh mục
@endsection

@section('content')
    @if(session('message'))
        <h4>{{session('message')}}</h4>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Ảnh</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->cover}}</td>
                <td>{{$item->is_active}}</td>
                <td>
                    <a href="{{route('categories.show', $item)}}">
                        <button class="btn btn-info">Xem</button>
                    </a>
                    <a href="{{route('categories.edit', $item)}}">
                        <button class="btn btn-success">Sửa</button>
                    </a>
                    <form action="{{route('categories.destroy', $item)}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">
                            Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$data->links()}}
@endsection
