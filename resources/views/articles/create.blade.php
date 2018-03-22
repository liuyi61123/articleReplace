@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/simditor.css') }}">
@endsection

@section('content')
<div class="container">
    <form action="{{route('articles.store')}}" method="post">
        <div class="row">
            @csrf
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">编辑文章</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="template_id">模板</label>
                            <select class="form-control" name="template_id" id="template_id">
                                <option value="1">模板1</option>
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="title">标题</label>
                            <input type="text" class="form-control" id="title" required name="title" placeholder="文章标题">
                         </div>
                         <div class="form-group">
                            <label for="keywords">关键字</label>
                            <input type="text" class="form-control" required name="keywords" id="keywords" placeholder="请输入关键字">
                         </div>
                         <div class="form-group">
                            <label for="description">描述</label>
                            <textarea id="description" class="form-control" required name="description" rows="8" cols="80" placeholder="请输入描述"></textarea>
                        </div>
                        <div class="form-group">
                            <label>内容</label>
                            <textarea id="editor" name="content" required rows="8" cols="80"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <param-setting></param-setting>
            </div>
        </div>
    </form>
</div>
<example-component></example-component>
@endsection
@section('scripts')
<script type="text/javascript"  src="{{ asset('js/module.js') }}"></script>
<script type="text/javascript"  src="{{ asset('js/hotkeys.js') }}"></script>
<script type="text/javascript"  src="{{ asset('js/uploader.js') }}"></script>
<script type="text/javascript"  src="{{ asset('js/simditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
       var editor = new Simditor({
           textarea: $('#editor'),
       });
    });
</script>
@endsection
