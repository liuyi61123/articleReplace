@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/simditor.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">编辑文章</div>

                <div class="card-body">
                    <form>
                      <div class="form-group">
                        <label for="title">标题</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="文章标题">
                      </div>
                      <div class="form-group">
                        <label for="keywords">关键字</label>
                        <input type="text" class="form-control" name="keywords" id="keywords" placeholder="请输入关键字">
                      </div>
                      <div class="form-group">
                        <label for="description">描述</label>
                        <textarea id="description" class="form-control" name="description" rows="8" cols="80" placeholder="请输入描述"></textarea>
                      </div>
                      <div class="form-group">
                        <label>内容</label>
                        <textarea id="editor" name="content" rows="8" cols="80"></textarea>
                      </div>
                      <button type="submit" class="btn btn-primary">提交</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <param-setting></param-setting>
        </div>
    </div>
</div>
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
