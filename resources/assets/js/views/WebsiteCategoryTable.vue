<template>
    <el-row :gutter="20">
        <el-col :span="24">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>网站分类</span>
                    <el-button type="success" size="small" style="float: right;" @click="createWebsiteCategory()">新建</el-button>
                </div>
                <div class="text item">
                   <el-tree
                        :data="treeData"
                        node-key="id"
                        default-expand-all
                        :expand-on-click-node="false">
                        <span class="custom-tree-node" slot-scope="{ node, data }">
                            <span>{{ node.label }}</span>
                            <span>
                            <el-button
                                type="text"
                                size="mini"
                                @click="() => editWebsiteCategory(node, data)">
                                编辑
                            </el-button>
                            <el-button
                                type="text"
                                size="mini"
                                @click="() => deleteWebsiteCategory(node, data)">
                                删除
                            </el-button>
                            </span>
                        </span>
                    </el-tree>
                </div>
            </el-card>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        data () {
            return {
                treeData:[],
                total:0,//总数
            }
        },
        methods: {
            //新建
            createWebsiteCategory(){
                window.location.href="/website-categories/create";
            },
            //编辑
            editWebsiteCategory(node, data){
                console.log(node)
                console.log(data)
                window.location.href="/website-categories/"+node.id+"/edit";
            },
            //删除
            deleteWebsiteCategory(node, data){
                axios.delete('/website-categories/'+node.id)
               .then((response)=> {
                   console.log(response)
                   let message = {};
                    if(response.data.status == 200){
                        this.$message({
                            message: '删除成功',
                            type: 'success'
                        });
                        //重新加载数据
                        const parent = node.parent
                        const children = parent.data.children || parent.data
                        const index = children.findIndex(d => d.id === data.id)
                        children.splice(index, 1)

                    }else if (response.data.status == 500) {
                        this.$message({
                            message: response.data.msg,
                            type: 'warning'
                        });
                    }else{
                        this.$message({
                            message: '删除失败',
                            type: 'error'
                        });
                    }
                   
               })
               .catch((error)=>{
                   console.log(error)
               })

                return false
                axios.delete('/website-categories/'+row.id)
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
           loadData(page){
               //加载table数据
               page = page || 1
               axios.get('/website-categories')
               .then((response)=> {
                   console.log(response)
                   this.treeData = response.data
               })
               .catch((error)=>{
                   console.log(error)
               })
           }
        },
        created(){
            //加载table数据
            this.loadData()
        }
    }
</script>
