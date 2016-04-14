#API Spac

1. [Create a Account](#CreateAccount)
2. [Auth Login](#AuthLogin)
3. [CheckEmail](#CheckEmail)
4. [Logout](#Logout)
5. [CreateBucket](#CreateBucket)
6. [CheckBucket](#CheckBucket)
7. [ListBuckets](#ListBuckets)



## 1.<a name="CreateAccount">Create a Account</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/auth/register</td>
    </tr>
</table>

###Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">Email</td>
        <td style="width:150px">email</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">password</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">password_confirmation</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
</table>

###Json Response
####Success
```
status code:200
{
  "id": *id*,
  "uid": *uid*,
  "email": *email*,
  "name": *name*,
  "created_at": *createTime*,
  "updated_at": *updateTime*
}
```
####Error
```
status code:422
{
  "message": "validator_error",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

## 2.<a name="AuthLogin">Auth Login</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/auth/login</td>
    </tr>
</table>

###Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">Email</td>
        <td style="width:150px">eamil</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">password</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
</table>

###Json Response
####Success
```
status code:200
{
  "id": *id*,
  "uid": *uid*,
  "email": *email*,
  "name": *name*,
  "created_at": *createTime*,
  "updated_at": *updateTime*,
  "token": *token*
}
```
####Error
```
status code:401
{
  "message": "verify_error"
}
```
## 3.<a name="CheckEmail">CheckEmail</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/auth/checkEmail</td>
    </tr>
</table>

###Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">Email</td>
        <td style="width:150px">eamil</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
</table>

###Json Response
####Success
```
status code:200
{
 	"message": "You can use the email"
}
```
####Error
```
status code:422
{
  "message": "validator_error",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

## 4.<a name="Logout">Logout</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/logout</td>
    </tr>
</table>


###Json Response
####Success
```
status code:200
{
  "message": "Invalidate Token Success"
}
```
####Error
```
status code:401
{
  "message": "Invalidate Token Error"
}
```

## 5.<a name="CreateBucket">CreateBucket</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/bucket/create</td>
    </tr>
</table>

###Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">bucket</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
</table>

###Json Response
####Success
```
status code:200
{
  "Buckets": [
    {
      "Name": "BucketName",
      "CreationDate": "2016-04-08T14:46:28.000Z"
    }
  ]
}
```
####Error
```
status code:401
{
  "message": "Create Bucket Error"
}
```

## 6.<a name="CheckBucket">CheckBucket</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/bucket/check</td>
    </tr>
</table>

###Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">bucket</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
</table>

###Json Response
####Success
```
status code:200
{
  "message": "You can use the bucket"
}
```
####Error
```
status code:401
{
  "message": "Has Bucket"
}
```

## 7.<a name="ListBuckets">ListBuckets</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/bucket/list</td>
    </tr>
</table>


###Json Response
####Success
```
status code:200
{
  "Buckets": [
    {
      "Name": "BucketName",
      "CreationDate": "2016-04-08T14:46:28.000Z"
    }
  ]
}
```
