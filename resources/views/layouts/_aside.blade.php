<div class="el-aside">
    <div class="website-title">
        <h2>文章系统</h2>
    </div>
    <el-menu
      style="border-right:solid 0px #e6e6e6"
      class="el-menu-vertica"
      background-color="rgb(48, 65, 86)"
      >
      <a href="{{ route('articles.index') }}">
          <el-menu-item index="1">
              <i class="el-icon-document"></i>
              <span slot="title">文章</span>
          </el-menu-item>
      </a>
       <a href="{{ route('templates.index') }}">
          <el-menu-item index="2">
              <i class="el-icon-printer"></i>
              <span slot="title">模板</span>
          </el-menu-item>
      </a>
      <a href="{{ route('original.index') }}">
         <el-menu-item index="2">
             <i class="el-icon-edit-outline"></i>
             <span slot="title">伪原创</span>
         </el-menu-item>
     </a>
    </el-menu>
</div>
