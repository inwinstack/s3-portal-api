# Admin API Reference Guide

1. [Create User](#CreateUser)
2. [List Users](#ListUsers)

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
        <td style="width:400px">/api/v1/admin/list</td>
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
  "message": "The email has already been taken"
}
- or -
status code:403
{
  "message": "Permission denied"
}
```
