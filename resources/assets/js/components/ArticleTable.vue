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
                          prop="id"
                          label="Id"
                          width="180">
                         </el-table-column>
                        <el-table-column
                          prop="template_id"
                          label="模板"
                          width="180">
                        </el-table-column>
                        <el-table-column
                          prop="config"
                          label="配置">
                        </el-table-column>
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
                </div>
            </el-card>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        data () {
            return {
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
           }
        },
        created(){
            //加载table数据
            axios.get('/articles')
            .then((response)=> {
                console.log(response);
                this.tableData = response.data.data;
            })
            .catch((error)=>{
                console.log(error);
            });
        }
    }
</script>
