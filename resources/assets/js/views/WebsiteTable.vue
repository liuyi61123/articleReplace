<template>
    <el-row :gutter="20">
        <el-col :span="24">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>网站列表</span>
                    <el-button type="success" size="small" style="float: right;" @click="createWebsite()">新建</el-button>
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
                          sortable
                          width="100">
                         </el-table-column>
                         <el-table-column
                           prop="name"
                           label="名称">
                         </el-table-column>
                        <el-table-column
                          prop="category.label"
                          label="分类">
                        </el-table-column>
                        <el-table-column
                          prop="url"
                          label="网址"
                          >
                        </el-table-column>
                        <el-table-column
                          prop="created_at"
                          label="添加时间"
                          sortable>
                        </el-table-column>
                        <el-table-column
                        fixed="right"
                        align="center"
                        label="操作">
                        <template slot-scope="scope">
                            <el-button-group>
                                <el-button
                              size="mini"
                              type="primary"
                              @click="editWebsite(scope.row)">编辑</el-button>
                            <el-button
                              size="mini"
                              type="warning"
                              @click="urlWebsite( scope.row)">URL</el-button>
                            <el-button
                                size="mini"
                                type="danger"
                                @click="deleteWebsite(scope.$index, scope.row)">删除</el-button>
                            </el-button-group>
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
        <el-dialog title="url管理" :visible.sync="dialogFormVisible">
            <el-upload
                class="upload-demo"
                ref="upload"
                action="/websites/upload"
                :on-preview="previewFile"
                :on-remove="removeFile"
                :on-success="uploadSuccess"
                list-type="text"
                name="upload_file"
                accept="text/plain"
                :limit="2"
                :data="uploadData"
                :headers="headers"
                :file-list="fileList"
                :auto-upload="false">
                <el-button slot="trigger" size="small" type="primary">选取文件</el-button>
                <el-button size="small" type="success" @click="submitUpload('replace')">覆盖</el-button>
                <el-button size="small" type="warning" @click="submitUpload('add')">追加</el-button>
                <div slot="tip" class="el-upload__tip">只能上传txt文件，一行一条</div>
            </el-upload>
            <div slot="footer" class="dialog-footer">
                <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
            </div>
        </el-dialog>
    </el-row>
    
</template>

<script>
    export default {
        data () {
            return {
                fileList:[],
                headers:{
                    'X-CSRF-TOKEN':document.head.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With':'XMLHttpRequest'
                },
                uploadData:{},
                dialogFormVisible:false,
                total:0,//总数
                current_page:1,//当前页数
                per_page:1,//每页显示数量
                tableData:[]
            }
        },
        methods: {
            //编辑url
            urlWebsite(row){
                this.dialogFormVisible = true
                this.uploadData.id = row.id
                if(row.urls){
                    //已经有url
                    this.fileList = [row.urls]
                }
            },
            //移除url
            removeFile(e){
                console.log(e)
                 axios.post('/websites/remove',{id:this.uploadData.id })
                .then(res=> {
                    this.fileList = []
                    this.getPage(this.current_page)
                })
            },
            submitUpload(type){
                this.uploadData.type = type
                this.$refs.upload.submit()
            },
            //
            previewFile(e){
                console.log(e)
                window.open(e.url)
            },
            //上传成功回掉
            uploadSuccess(e){
                console.log(e)
                if(e.status == 200){
                    this.fileList = [e.data]
                }
            },
            //新建
            createWebsite(){
                window.location.href="/websites/create";
            },
            //编辑
            editWebsite(row){
                window.location.href="/websites/"+row.id+"/edit";
            },
            //删除
            deleteWebsite(index, row){
                axios.delete('/websites/'+row.id)
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
               axios.get('/websites?page='+page)
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
            console.log()
        }
    }
</script>
