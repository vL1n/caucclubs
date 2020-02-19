# -- coding: utf-8 --
import sys,json,requests


# 请求函数

def dataRequest(dep, arr, date):

    # 请求部分
    # 定义url,携程的机票数据接口
    url = "https://flights.ctrip.com/itinerary/api/12808/products"

    # 定义请求头
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
        'Content-Type': 'application/json',
    }

    # 定义请求主题
    data = {
        "flightWay":            "Oneway",
        "classType":            "ALL",
        "hasChild":             'false',
        "hasBaby":              'false',
        "searchIndex":          1,
        "airportParams":[{
            "dcity":            dep,
            "acity":            arr,
            "date":             date,
        }],
        "token":                "b7a37fea8ea6f5fa22ebf718d45207b4"
    }

    # 开始请求，res为请求到的数据
    res = requests.session().post(url=url, headers=headers, data=json.dumps(data))
    res_data = json.loads(res.text)
    res_data_test = 'success'
    return res_data_test

print(dataRequest(dep=sys.argv[1], arr=sys.argv[2], date=sys.argv[3]))
