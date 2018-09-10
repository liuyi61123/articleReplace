<el-header style="padding:0px;background:#fff;">
    <el-dropdown class="user-name-dropdown">
      <span class="el-dropdown-link">
        {{ Auth::user()->name }}<i class="el-icon-arrow-down el-icon--right"></i>
      </span>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item>
            <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            {{ __('退出') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
</el-header>
