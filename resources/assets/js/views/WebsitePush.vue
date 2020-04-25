<template>
    <el-row :gutter="20">
        <el-form label-width="100px" v-loading="loading">
            <el-col :span="22">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>自动提交</span>
                        <el-button type="success" size="small" style="float: right;" @click="addParam()">添加网站</el-button>
                    </div>
                    <el-form-item label="任务名称">
                        <el-input v-model="push.name"></el-input>
                    </el-form-item>
                    <el-form-item label="是否自动提交">
                        <el-switch
                            v-model="push.is_automatic">
                        </el-switch>
                    </el-form-item>
                    <el-form-item label="延时秒数">
                        <el-input-number v-model="push.delay" :min="1" :max="1000" label="延时秒数"></el-input-number>
                    </el-form-item>
                    <div class="box-card">
                        <el-card v-for="(item,index) in push.config" :key="item.id">
                            <div slot="header" class="clearfix"> 
                                <el-button style="float: right;" size="small" type="danger" icon="el-icon-delete" @click="deleteParam(index)"></el-button>
                            </div>
                            <div>
                                <el-form-item label="网站" prop="website_id">
                                    <el-select class="select" v-model="push.config[index].website_id" placeholder="请选择">
                                        <el-option
                                            v-for="item in websites"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="名称">
                                    <el-checkbox-group :min="1" v-model="push.config[index].platforms">
                                        <el-checkbox v-for="platform of platforms" :label="platform.id" :key="platform.id" @change="checkedPlatform">{{platform.name}}</el-checkbox>
                                    </el-checkbox-group>
                                </el-form-item>

                                <el-form-item label="条数">
                                    <el-input-number v-model="push.config[index].number" :min="1" :max="1000" label="条数"></el-input-number>
                                </el-form-item>
                            </div>
                        </el-card>

                        <el-form-item>
                            <el-button type="primary" @click="submitFrom">提交</el-button>
                        </el-form-item>
                    </div>
                </el-card>
            </el-col>
        </el-form>
    </el-row>

</template>

<script>
    export default {
        props:['id'],
        data () {
            return {
                push:{
                    type:'automatic',
                    is_automatic:false,
                    delay:5,
                    name:'',
                    config:[
                        {
                            website_id:'',
                            platforms:[],
                            number:1
                        }
                    ],
                },
                platforms:[
                    {
                        id:'baidu_zz',
                        name:'百度站长'
                    },
                    {
                        id:'shenma',
                        name:'神马'
                    },
                    {
                        id:'baidu_xz_t',
                        name:'百度熊掌(天级)'
                    },
                    {
                        id:'baidu_xz_z',
                        name:'百度熊掌(周级)'
                    },
                ],
                websites:[],
                loading:false,
            }
        },
        methods: {
            addParam(){
                //添加参数
                this.push.push({
                    website_id:'',
                    platforms:[],
                    number:1
                })
            },
            deleteParam(index){
                //删除参数
                this.push.splice(index, 1)
            },
            checkedPlatform(e){
                console.log(e)
            },
            submitFrom(){
                axios({
                    method: this.id?'put':'post',
                    url: this.id?'/website-pushes/'+this.id:'/website-pushes',
                    data:this.push
                })
                .then((response)=> {
                    let message = {};
                    if(response.data.status == 200){
                        this.$message({
                            message: '提交成功',
                            type: 'success'
                        });
                        window.location.href="/website-pushes"
                    }else{
                        this.$message({
                            message: '保存失败',
                            type: 'error'
                        });
                    }
                })
                .catch((error)=>{
                    console.log(error)
                    let message = ''
                    let status = error.response.status
                    if (status == 422) {
                        message = error.response.data.message

                    } else if (status == 403) {
                        message = '权限不足'
                    }
                    else if (status == 419) {
                        message = '非法请求'
                    }
                    else {
                        message = '系统错误:' + status
                    }
                    this.loading = false
                    this.$message.error(message)
                });
            },
            getWebsites(){
                axios({
                    method: 'get',
                    url: '/websites/api',
                })
                .then(response=> {
                    this.websites = response.data
                })
            }
        },
        created(){
            this.getWebsites()
            //
            if(this.id){
                //读取要编辑的文章数据
                axios.get('/website-pushes/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response)
                    let data = response.data
                    this.push = {
                        type:'automatic',
                        is_automatic:data.is_automatic,
                        delay:data.delay,
                        name:data.name,
                        config:data.config,
                    }
                })
                .catch((error)=>{
                    console.log(error)
                });
            }
        }
    }
</script>

<style lang="css">
    .input-with-select{
        width: 100px;
    }
    .select{
        display: block;
    }
</style>