<template>
    <el-row :gutter="20">
        <el-col :span="24">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>伪原创</span>
                </div>
                <div class="text item">
                    <el-form ref="form" :model="form" :rules="rules" label-width="100px">
                      <el-form-item label="文章目录" prop="start_path">
                        <el-input v-model="form.start_path">
                          <template slot="prepend">storage/app/public/original/start/start_path/</template>
                        </el-input>
                      </el-form-item>
                      <el-form-item label="保存目录"  prop="over_path">
                        <el-input v-model="form.over_path">
                            <template slot="prepend">storage/app/public/original/start/over_path/</template>
                        </el-input>
                      </el-form-item>
                      <el-form-item label="相关词" prop="th">
                        <el-input-number :min="1" v-model="form.th"></el-input-number >
                      </el-form-item>
                      <el-form-item>
                        <el-button type="primary" @click="submitForm('form')">执行</el-button>
                      </el-form-item>
                    </el-form>
                </div>
            </el-card>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        data () {
            return {
                form:{
                    start_path:'',
                    over_path:'',
                    th:3,
                },
                rules:{
                    start_path:[
                        { required: true, message: '文章目录', trigger: 'blur' },
                        { min: 1, max: 100, message: '长度在 1 到 100 个字符', trigger: 'blur' }
                    ],
                    over_path:[
                        { required: true, message: '保存目录', trigger: 'blur' },
                        { min: 1, max: 100, message: '长度在 1 到 100 个字符', trigger: 'blur' }
                    ],
                    th:[
                        { required: true, message: '相关词', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            submitForm(formName){

                let data = this.form
                this.$refs[formName].validate((valid) => {
                  if (valid) {
                      axios.post('/original',data)
                      .then((response)=> {
                          console.log(res)
                          this.$message({
                              message: '正在生成中',
                              type: 'success'
                          });
                      })
                      .catch((error)=>{
                         console.log(error);
                         this.loading = false;
                         this.$message.error('错了哦，这是一条错误消息');
                     })
                  } else {
                    console.log('error submit!!');
                    return false;
                  }
                })
            }
        },
        created(){

        }
    }
</script>
