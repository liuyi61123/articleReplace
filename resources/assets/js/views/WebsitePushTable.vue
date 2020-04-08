<template>
    <el-row :gutter="20">
        <el-col :span="24">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>提交记录</span>
                    <el-button type="success" size="small" style="float: right;" @click="pushWebsite()">新建</el-button>
                </div>
                <div class="text item">
                    <el-table
                    :data="tableData"
                    border
                    style="width: 100%">
                        <el-table-column
                          align="center"
                          prop="id"
                          label="Id"
                          sortable>
                         </el-table-column>
                         <el-table-column
                          align="center"
                          prop="name"
                          label="名称"
                          >
                        </el-table-column>
                        <el-table-column
                          align="center"
                          prop="is_automatic"
                          label="是否自动执行"
                          sortable>
                          <template slot-scope="scope">
                              <el-tag v-if="scope.row.is_automatic" type="success">是<i class="el-icon-success"></i></el-tag>
                              <el-tag v-else type="danger">否<i class="el-icon-error"></i></el-tag>
                          </template>
                         </el-table-column>
                         <el-table-column
                          prop="status"
                          label="状态"
                          sortable>
                          <template slot-scope="scope">
                              <el-tag v-if="scope.row.status == 0" type="info">待运行<i class="el-icon-info"></i></el-tag>
                              <el-tag v-else-if="scope.row.status == 1" type="warning">生成中<i class="el-icon-loading"></i></el-tag>
                              <el-tag v-else-if="scope.row.status == 2" type="danger">已停止<i class="el-icon-error"></i></el-tag>
                              <el-tag v-else type="success">已完成<i class="el-icon-success"></i></el-tag>
                          </template>
                         </el-table-column>
                         <el-table-column
                          prop="delay"
                          label="延时秒数"
                          align="center"
                          sortable>
                         </el-table-column>
                         <el-table-column
                          prop="error"
                          label="错误"
                          >
                          <template slot-scope="scope">
                            <el-button v-show="scope.row.error" @click="viewError(scope.row.error)" type="text" size="small">查看详情</el-button>
                          </template>
                         </el-table-column>
                        <el-table-column
                          width="200"
                          align="center"
                          prop="created_at"
                          label="创建时间"
                          sortable>
                        </el-table-column>
                        <el-table-column
                          width="200"
                          align="center"
                          prop="updated_at"
                          label="最后更新时间"
                          sortable>
                        </el-table-column>
                        <el-table-column
                        width="250"
                        fixed="right"
                        align="center"
                        label="操作">
                        <template slot-scope="scope">
                             <el-button
                              :disabled="scope.row.status == 1"
                              size="mini"
                              type="primary"
                              @click="editWebsitePush(scope.row)">编辑</el-button>
                            <el-button
                              v-show="scope.row.status != 1"
                              size="mini"
                              type="warning"
                              @click="updateWebsitePush(scope.$index, scope.row,1)">执行</el-button>
                            <el-button
                            v-show="scope.row.status == 1"
                              size="mini"
                              type="warning"
                              @click="updateWebsitePush(scope.$index, scope.row,2)">停止</el-button>
                            <el-button
                                :disabled="scope.row.status == 1"
                                size="mini"
                                type="danger"
                                @click="deleteWebsitePush(scope.$index, scope.row)">
                                删除
                            </el-button>
                        </template>
                      </el-table-column>
                    </el-table>
                    <el-pagination
                      background
                      layout="prev, pager, next"
                      :current-page.sync="current_page"
                      :page-size.sync="per_page"
                      :total="total"
                      @prev-click="prevClick"
                      @next-click="nextClick"
                      @current-change="currentChange"
                      >
                    </el-pagination>
                </div>
            </el-card>
        </el-col>

        <el-dialog title="错误详情" :visible.sync="dialogFormVisible">
            <pre>{{checkError}}</pre>
        </el-dialog>
    </el-row>
    
</template>

<script>
    export default {
        data () {
            return {
                dialogFormVisible:false,
                total:0,//总数
                current_page:1,//当前页数
                per_page:1,//每页显示数量
                tableData:[]
            }
        },
        methods: {
            //新建
            pushWebsite(){
                window.location.href="/website-pushes/create"
            },
            //删除
            deleteWebsitePush(index, row){
                axios.delete('/website-pushes/'+row.id)
               .then((response)=> {
                    console.log(response);
                    let message = {};
                    if(response.data.status == 200){
                        this.$message({
                            message: '删除成功',
                            type: 'success'
                        });
                        //重新加载数据
                        this.tableData.splice(index, 1);
                    }else{
                        this.$message({
                            message: '删除失败',
                            type: 'error'
                        });
                    }
               })
               .catch((error)=>{
                   console.log(error);
                   this.$message.error('错了哦，这是一条错误消息');
               });
           },
           updateWebsitePush(index,row,status){
               axios({
                    method: 'put',
                    url: '/website-pushes/'+row.id,
                    data:{
                        status:status
                    }
                })
               .then((response)=> {
                    console.log(response);
                    let message = {};
                    if(response.data.status == 200){
                        this.$message({
                            message: response.data.msg,
                            type: 'success'
                        });
                        //重新加载数据
                        this.tableData[index].status = status
                    }else{
                        this.$message({
                            message: response.data.msg,
                            type: 'error'
                        });
                    }
               })
               .catch((error)=>{
                   console.log(error)
                   this.$message.error('错了哦，这是一条错误消息');
               });
           },
           //编辑
           editWebsitePush(row){
                window.location.href="/website-pushes/"+row.id+"/edit"
           },
           //查看错误详情
           viewError(error){
                this.dialogFormVisible = true
                this.checkError = error
           },
           currentChange(page){
               this.getPage(page)
           },
           prevClick(page){
               this.getPage(page)
           },
           nextClick(page){
               this.getPage(page)
           },
           getPage(page){
               //加载table数据
               page = page || 1
               axios.get('/website-pushes?page='+page)
               .then((response)=> {
                   console.log(response)
                   this.total = response.data.total
                   this.current_page = response.data.current_page
                   this.per_page = response.data.per_page
                   this.tableData = []
                   response.data.data.map((value,index)=>{
                       this.tableData.push(value)
                   })
               })
               .catch((error)=>{
                   console.log(error)
               })
           }
        },
        created(){
            //加载table数据
            this.getPage(1)
        }
    }
</script>
