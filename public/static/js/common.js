function checkStatus() {
    $.get('/index/index/checkStatus', function (res) {
        if(res=='1'){
            showOne()
        }
        else{
            showTwo()
        }
    }, 'json')

}
checkStatus()

function showOne() {
    let form = $('#userStatus')
    form.append(
        ` <li><a href="/index/profile">个人资料</a></li>
              <li><a href="/index/login/logout">注销账号</a></li>`
    )
}

function showTwo() {
    let form = $('#userStatus')
    form.append(
        `<li><a href="/index/login">登陆</a></li>
             <li><a href="/index/register">注册</a></li>`
    )
}
function getUserName() {
    $.get('/index/Login/getUserName',function (res) {
        //window.onload = alert(res);
        if(res){
            document.getElementById('usernm').innerHTML = res;
        }else {
            document.getElementById('usernm').innerHTML = '未登录';
        }
    },'json')
}
getUserName()

