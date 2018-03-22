<template>
    <div class="card">
        <div class="card-header clearfix">
            <span class="float-left">参数设置</span>
            <button type="button" class="btn btn-success btn-sm float-right" @click="addParam()">添加参数</button>
        </div>
        <div class="card-body">
            <div class="" v-for="(param,index) in params">
                <div class="form-group">
                    <input type="text" required class="form-control" name="param_names[]" v-model="param.name" placeholder="参数名称{$param}">
                </div>
                <div class="form-group">
                    <textarea class="form-control" required name="param_contents[]" rows="5" cols="30" v-model="param.content"   placeholder="需要替换的内容,一行一个"></textarea>
                </div>
                <button type="button" class="btn btn-danger btn-sm float-right" @click="deleteParam(index)">删除</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                params:
                [
                    {
                        id:0,
                        name:'',
                        content:''
                    }
                ],
                index:0
            }
        },
        methods: {
            addParam(){
                //添加参数
                this.index++;
                Vue.set(this.params, this.params.length, {id:this.index,name:'',content:''});
            },
            deleteParam(index){
                //删除参数
                this.params.splice(index, 1);
            }
        }
    }
</script>
