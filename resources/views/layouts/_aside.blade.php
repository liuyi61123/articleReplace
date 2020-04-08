<div class="el-aside">
    <div class="website-title">
        <h2>牛广工具</h2>
    </div>
    <el-menu
      style="border-right:solid 0px #e6e6e6"
      class="el-menu-vertica"
      background-color="rgb(48, 65, 86)"
      text-color="#fff"
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
         <el-menu-item index="3">
             <i class="el-icon-edit-outline"></i>
             <span slot="title">伪原创</span>
         </el-menu-item>
     </a>
     <a href="{{ route('baidu_urls.index') }}">
        <el-menu-item index="4">
            <i class="el-icon-edit-outline"></i>
            <span slot="title">百度URL</span>
        </el-menu-item>
    </a>
    <el-submenu index="5">
        <template slot="title">
          <i class="el-icon-s-platform"></i>
          <span>网站自提交</span>
        </template>
        <el-menu-item-group>
            <a href="{{ route('websites.index') }}">
                <el-menu-item index="5-1">网站管理</el-menu-item>
            </a>
            <a href="{{ route('website-categories.index') }}">
                <el-menu-item index="5-2">网站分类</el-menu-item>
            </a>
            <a href="{{ route('website-pushes.manual') }}">
                <el-menu-item index="5-3">手动推送</el-menu-item>
            </a>
            <a href="{{ route('website-pushes.index') }}">
                <el-menu-item index="5-4">自动推送</el-menu-item>
            </a>
        </el-menu-item-group>
      </el-submenu>
    </el-menu>
</div>
