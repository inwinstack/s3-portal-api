# Admin API Reference Guide

1. [Create User](#CreateUser)
2. [List Users](#ListUsers)
3. [Reset Password User](#ResetPasswordUser)
4. [Update Role User](#UpdateRoleUser)
5. [Delete User](#DeleteUser)

## 1.<a name="CreateUser">Create User</a>

<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/admin/create</td>
    </tr>
</table>

### Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
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
</table>

### JSON Response
#### Success
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

#### Error
```
status code:403
{
  "message": "The email has already been taken"
}
- or -
status code:403
{
  "message": "Permission denied"
}
```

## 2.<a name="ListUsers">List Users</a>
<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">GET</td>
        <td style="width:400px">/api/v1/admin/list/{page}</td>
    </tr>
</table>

### JSON Response
#### Success
```
status code:200
{
    "users" [
      {
    	  "id": *id*,
		  "uid": *uid*,
		  "email": *email*,
		  "name": *name*,
		  "role": *role*,
		  "created_at": *createTime*,
		  "updated_at": *updateTime*,
		  "used_size_kb": *usedSizeKB*,
		  "total_size_kb": *totalSizeKB*
      }
      ...
    ],
    "count": *user count*
}
```

#### Error
```
status code:403
{
  "message": "The page value is not incorrect"
}
- or -
status code:403
{
  "message": "Permission denied"
}
```

## 3.<a name="ResetPasswordUser">Reset Password User</a>
<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/admin/reset</td>
    </tr>
</table>

### Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
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
</table>

### JSON Response
#### Success
```
status code:200
{
  "Users": [
    {
    	"id": *id*,
		"uid": *uid*,
		"email": *email*,
		"name": *name*,
		"role": *role*,
		"created_at": *createTime*,
		"updated_at": *updateTime*
    }
  ]
}
```

#### Error
```
status code:403
{
  "message": "The email does not exist"
}
- or -
status code:403
{
  "message": "Permission denied"
}
```

## 4.<a name="UpdateRoleUser">Update Role User</a>
<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">POST</td>
        <td style="width:400px">/api/v1/admin/role</td>
    </tr>
</table>

### Input Parameter

<table>
    <tr>
        <td style="width:50px">Type</td>
        <td style="width:150px">Name</td>
        <td style="width:50px">Require</td>
        <td style="width:100px">Remark</td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">email</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px"></td>
    </tr>
    <tr>
        <td style="width:50px">String</td>
        <td style="width:150px">role</td>
        <td style="width:50px">✔︎</td>
        <td style="width:100px">admin or user</td>
    </tr>
</table>

### JSON Response
#### Success
```
status code:200
{
  "Users": [
    {
    	"id": *id*,
		"uid": *uid*,
		"email": *email*,
		"name": *name*,
		"role": *role*,
		"created_at": *createTime*,
		"updated_at": *updateTime*
    }
  ]
}
```

#### Error
```
status code:403
{
  "message": "The email does not exist"
}
- or -
status code:403
{
  "message": "Permission denied"
}
```

## 5.<a name="DeleteUser">Delete User</a>
<table>
    <tr>
        <td style="width:50px">Method</td>
        <td style="width:400px">URI</td>
    </tr>
    <tr>
        <td style="width:50px">DELETE</td>
        <td style="width:400px">/api/v1/admin/delete/{email}</td>
    </tr>
</table>

### JSON Response
#### Success
```
status code:200
{
  "message": "The user has been deleted"
}
```

#### Error
```
status code:403
{
  "message": "The email does not exist"
}
- or -
status code:403
{
  "message": "Permission denied"
}
- or -
status code:403
{
  "message": "The delete user operation failed"
}
```


