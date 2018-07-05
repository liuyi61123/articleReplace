<template>
    <el-row :gutter="20">
        <el-form label-width="80px">
            <el-col :span="16">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="市">
                            <el-select style="width:100%" v-model="article.city" placeholder="请选择" @change="changeCity">
                                <el-option  v-for="city in citys"
                                  :key="city.id"
                                  :label="city.name"
                                  :value="city.id">
                              </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="区">
                            <el-checkbox-group :min="1" v-model="article.countys">
                                  <el-checkbox v-for="county of countys" :label="county.name" :key="county.id"></el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="类型">
                            <el-select style="width:100%" v-model="article.type" placeholder="请选择">
                                <el-option  v-for="type in types"
                                  :key="type.id"
                                  :label="type.name"
                                  :value="type.id">
                              </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="品牌">
                            <el-checkbox-group :min="1" v-model="article.cars">
                                  <el-checkbox v-for="car of cars" :label="car.name" :key="car.id"></el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="模板">
                            <el-select style="width:100%" v-model="article.template_id" placeholder="请选择">
                                <el-option  v-for="template in templates"
                                  :key="template.id"
                                  :label="template.name"
                                  :value="template.id">
                              </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="submitFrom()">保存</el-button>
                        </el-form-item>
                    </div>
                </el-card>
            </el-col>
            <el-col :span="8">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>额外参数</span>
                        <el-button type="success" size="small" style="float: right;" @click="addParam()">添加参数</el-button>
                    </div>

                    <div class="text item">
                        <div v-for="(param,index) in article.params">
                            <el-form-item label="参数名称">
                                <el-input v-model="param.name" placeholder="{$param}"></el-input>
                            </el-form-item>
                            <el-form-item label="参数内容">
                                <el-select
                                    v-model="param.content"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    placeholder="请选择文章标签">
                                  </el-select>
                            </el-form-item>
                            <el-form-item>
                                <el-button style="float: right;" size="small" type="danger" icon="el-icon-delete" @click="deleteParam(index)"></el-button>
                            </el-form-item>
                        </div>
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
                templates:[],
                types:[
                    {
                        id:1,
                        name:'车贷'
                    },
                    {
                        id:2,
                        name:'房贷'
                    }
                ],
                citys:[],
                countys:[],
                cars:[],
                article:{
                    template_id:1,
                    countys:[],
                    type:1,
                    city:1,
                    cars:[],
                    params:
                    [
                        {
                            name:'',
                            content:[]
                        }
                    ],
                },
                paramsIndex:0,
                title:'',
                loading:''
            }
        },
        methods: {
            changeCity(e){
                this.getCountys(e)
            },
            submitFrom(){
                this.fullScreen(true)
                // 发送 POST 请求
                axios({
                    method: this.id?'put':'post',
                    url: this.id?'/articles/'+this.id:'/articles',
                    data:this.article
                })
                .then((response)=> {
                        this.fullScreen(false)
                        let message = {};
                        if(response.data.status == 200){
                            this.$message({
                                message: '保存成功',
                                type: 'success'
                            });
                            window.location.href="/articles";
                        }else{
                            this.$message({
                                message: '保存失败',
                                type: 'error'
                            });
                        }
                   })
                   .catch((error)=>{
                       console.log(error)
                       this.fullScreen(false)
                       this.$message.error('错了哦，这是一条错误消息')
                   });
            },
            addParam(){
                //添加参数
                this.paramsIndex++
                Vue.set(this.article.params, this.article.params.length, {name:'',content:[]})
            },
            deleteParam(index){
                //删除参数
                this.article.params.splice(index, 1)
            },
            //获取模板列表
            getTemplates(){
                axios.get('/templates')
                .then((response)=> {
                    this.templates = response.data
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取市区信息
            getCitys(id){
                axios.get('/articles/citys/'+id)
                .then((response)=> {
                    this.citys = response.data
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取市区信息
            getCountys(id){
                axios.get('/articles/citys/'+id)
                .then((response)=> {
                    this.countys = response.data
                    if(!this.id){
                        response.data.map((value,index)=>{
                            this.article.countys.push(value.name)
                        })
                    }
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取品牌信息
            getCars(id){
                axios.get('/articles/cars/'+id)
                .then((response)=> {
                    this.cars = response.data
                    if(!this.id){
                        response.data.map((value,index)=>{
                            this.article.cars.push(value.name)
                        })
                    }
                })
                .catch((error)=>{
                    console.log(error)
                });
            },
            fullScreen(bool) {
                if(bool){
                    this.loading = this.$loading({
                      lock: true,
                      text: 'Loading',
                      spinner: 'el-icon-loading',
                      background: 'rgba(0, 0, 0, 0.7)'
                    });
                }else{
                    this.loading.close();
                }
            }
        },
        created(){
            //开启loading
            this.fullScreen(true)

            //编辑的情况
            if(this.id){
                this.title = '编辑文章';
                //读取要编辑的文章数据
                axios.get('/articles/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response.data);
                    this.article.template_id = response.data.template_id
                    this.article.cars = response.data.config.cars
                    this.article.type = response.data.config.type
                    this.article.city = response.data.config.city
                    this.getCountys(response.data.config.city)
                    this.article.countys = response.data.config.countys
                    this.article.params = response.data.config.params

                })
                .catch((error)=>{
                    console.log(error)
                });
            }else{
                this.title = '新建文章';
            }

            //获取模板列表
            this.getTemplates()
            this.getCars(0)
            this.getCitys(0)
            this.getCountys(1)
            //关闭loading
            this.fullScreen(false)
        }
    }
</script>
