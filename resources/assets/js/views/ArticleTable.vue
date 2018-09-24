<template>
    <el-row :gutter="20">
        <el-col :span="24">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>文章列表</span>
                    <el-button type="success" size="small" style="float: right;" @click="createArticle()">新建</el-button>
                </div>
                <div class="text item">
                    <el-table
                    :data="tableData"
                    border
                    style="width: 100%">
                        <el-table-column
                          type="selection"
                          width="55">
                        </el-table-column>
                        <el-table-column
                          prop="status"
                          label="状态"
                          sortable
                          width="100">
                          <template slot-scope="scope">
                              <el-tag v-if="scope.row.status == 1" type="success">已完成<i class="el-icon-success"></i></el-tag>
                              <el-tag v-else type="danger">生成中<i class="el-icon-loading"></i></el-tag>
                          </template>
                         </el-table-column>
                        <el-table-column
                          prop="id"
                          label="Id"
                          sortable
                          width="100">
                         </el-table-column>
                         <el-table-column
                           prop="name"
                           label="名称">
                         </el-table-column>
                        <el-table-column
                          prop="template.name"
                          label="模板">
                        </el-table-column>
                        <el-table-column
                          prop="template.updated_at"
                          label="更新时间"
                          sortable>
                        </el-table-column>
                        <!-- <el-table-column
                          prop="config"
                          label="配置">
                          <template slot-scope="scope">
                              {{scope.row.config}}
                          </template>
                        </el-table-column> -->
                        <el-table-column
                        fixed="right"
                        align="center"
                        label="操作">
                        <template slot-scope="scope">
                            <el-button
                              size="mini"
                              type="primary"
                              @click="editArticle(scope.$index, scope.row)">编辑</el-button>
                            <el-button
                              size="mini"
                              type="warning"
                              @click="exportArticle(scope.$index, scope.row)">导出</el-button>
                              <el-button
                                size="mini"
                                type="danger"
                                @click="deleteArticle(scope.$index, scope.row)">删除</el-button>
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
    </el-row>
</template>

<script>
    export default {
        data () {
            return {
                total:0,//总数
                current_page:1,//当前页数
                per_page:1,//每页显示数量
                tableData:[]
            }
        },
        methods: {
            //新建
            createArticle(){
                window.location.href="/articles/create";
            },
            //编辑
            editArticle(index, row){
                window.location.href="/articles/"+row.id+"/edit";
            },
            //导出zip
            exportArticle(index, row){
                window.location.href="/articles/export/"+row.id;
            },
            //删除
            deleteArticle(index, row){
                axios.delete('/articles/'+row.id)
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
                   this.loading = false;
                   this.$message.error('错了哦，这是一条错误消息');
               });
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
               axios.get('/articles?page='+page)
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
