# 主站点 `a.com` SSO 集成接口规范（适配已配置 `b.a.com` 场景）

---
您可以在`src\Routes\Api.php`中搜索`api/sso`查看主站点接口示例
您可以在底部查看简化文档

## 一、概述  
本文档聚焦主站点 `http://a.com` 需实现的 SSO 核心接口，适配子站点 `http://b.a.com` 已完成的前端跳转与后端逻辑（如回调接收、`code` 换 `token`、用户信息同步等）。通过以下接口，`a.com` 将为 `b.a.com` 提供身份验证、令牌发放及用户信息查询能力，完成跨域单点登录。

您需要先在子站点 `http://b.a.com` 的`后台管理-设置-授权登录-SSO帐号`中配置好以下表单内容
``` js
SSO 主URL //填写您的主站点URL，如：http://a.com
SSO 主名称 //填写您的主站点名称，如：MaterialDesignForum
客户端ID //填写您主站信任的应用ID，如：mdf-b-a-com
客户端密钥 //填写您主站信任的密钥，如：1234567890abcdef1234567890abcdef
```
---

## 二、核心接口定义  

### 2.1 SSO 授权接口（`GET /api/sso/authorize`）  
**功能**：接收 `b.a.com` 的登录请求，验证合法性后引导用户完成主站身份验证，最终重定向回 `b.a.com` 并携带授权码 `code`。  请注意：如果您的主站点用户没有登录，必须先跳转登录授权再回来生成`code`，登录流程可参考微软、Github的登录流程

#### 接口详情  
- **方法**：`GET`  
- **路径**：`http://a.com/api/sso/authorize`  
- **前置条件**：`b.a.com` 已向 `a.com` 注册为合法应用（即 `client_id` 已在 `a.com` 后台备案）。  

#### 请求参数（查询参数，需 URL 编码）  
| 参数名         | 类型   | 是否必填 | 描述                                                                 |
|----------------|--------|----------|----------------------------------------------------------------------|
| `client_id`    | string | 是       | `b.a.com` 在 `a.com` 注册的应用 ID（如：`mdf-b-a-com`）              |
| `redirect_uri` | string | 是       | 授权成功后回调的 `b.a.com` 地址（固定为：`http://b.a.com/api/oauth/redirect/sso`，需与 `a.com` 后台备案的回调地址一致） |
| `response_type`| string | 否       | 固定为 `code`（表示返回授权码模式）                                   |
| `scope`        | string | 否       | 请求的用户信息范围（默认值：`openid profile email`，需与 `a.com` 支持的范围匹配） |

#### 处理逻辑  
1. **验证 `client_id`**：检查 `client_id` 是否在 `a.com` 注册，未注册则返回 `400 Bad Request`（错误信息如：`invalid_client_id`）。  
2. **验证 `redirect_uri`**：校验 `redirect_uri` 是否与 `client_id` 备案的回调地址一致（防钓鱼），不一致则返回 `400 Bad Request`（错误信息如：`invalid_redirect_uri`）。  
3. **用户身份验证**：若当前用户在 `a.com` 未登录，重定向至 `a.com` 登录页（如：`http://a.com/login?from_sso=1&client_id=xxx&redirect_uri=xxx`）；若已登录，直接进入下一步。  
4. **生成授权码 `code`**：用户验证通过后，生成短时效（建议 5 分钟）、一次性使用的随机码 `code`，并与 `client_id` 绑定存储（防止重复使用）。  

#### 响应行为  
验证通过后，重定向至 `redirect_uri` 并附加 `code` 参数，示例：  
`http://b.a.com/api/oauth/redirect/sso?code=AUTH_CODE_123456`（`AUTH_CODE_123456` 为生成的授权码）。  

---

### 2.2 访问令牌接口（`POST /api/sso/token`）  
**功能**：`b.a.com` 后端通过 应用ID`client_id` 应用秘钥`client_secret` 授权码 `code` 换取长期有效的访问令牌 `access_token`。  

#### 接口详情  
- **方法**：`POST`  
- **路径**：`http://a.com/api/sso/token`  
- **Content-Type**：`application/x-www-form-urlencoded`（表单编码）  

#### 请求参数（请求体）  
| 参数名         | 类型   | 是否必填 | 描述                                                                 |
|----------------|--------|----------|----------------------------------------------------------------------|
| `client_id`    | string | 是       | `b.a.com` 的应用 ID（同 `authorize` 接口的 `client_id`）            |
| `client_secret`| string | 是       | `b.a.com` 的应用密钥（`a.com` 需校验与 `client_id` 的绑定关系）      |
| `code`         | string | 是       | 授权码（来自 `authorize` 接口重定向的 `code` 参数）                  |
| `grant_type`   | string | 是       | 固定为 `authorization_code`（授权码模式）                            |
| `scope`        | string | 否       | 请求的用户信息范围（需与 `authorize` 接口的 `scope` 一致，默认：`openid profile email`） |

#### 处理逻辑  
1. **验证 `client_id` 和 `client_secret`**：检查二者是否为 `a.com` 备案的有效应用凭证，无效则返回 `401 Unauthorized`（错误信息如：`invalid_client`）。  
2. **验证 `code`**：检查 `code` 是否存在、未过期且未被使用，且与 `client_id` 绑定，无效则返回 `400 Bad Request`（错误信息如：`invalid_code`）。  
3. **生成 `access_token`**：验证通过后，生成长期有效的 `access_token`（建议有效期 1 小时），并关联用户身份信息。  

#### 成功响应（JSON）  
```json
{
  "access_token": "TOKEN_789ABC",       // 访问令牌（JWT 或随机字符串）
  "token_type": "Bearer",               // 固定值
  "scope": "openid profile email"      // 实际授权范围
}
```

---

### 2.3 用户信息接口（`POST /api/sso/user`）  
**功能**：`b.a.com` 后端通过 `access_token` 获取用户身份信息（`id`、`name`、`email`），用于本地账号自动登录或绑定。  

#### 接口详情  
- **方法**：`POST`  
- **路径**：`http://a.com/api/sso/user`  
- **认证方式**：推荐通过 `Authorization` 请求头传递 `access_token`（更安全），兼容请求体传递。  

#### 请求参数  
**方式 1（未兼容）：请求头传递-！！！请注意请求头方法暂时不可用，请使用post参数传递access_token**  
`Authorization: Bearer TOKEN_789ABC`（`TOKEN_789ABC` 为 `token` 接口返回的 `access_token`）  

**方式 2（推荐）：请求体传递**  
| 参数名         | 类型   | 是否必填 | 描述                     |
|----------------|--------|----------|--------------------------|
| `access_token` | string | 是       | 访问令牌（来自 `token` 接口） |

#### 处理逻辑  
1. **验证 `access_token`**：解析令牌（如 JWT）或查询缓存/数据库，验证令牌有效性及是否过期，无效则返回 `401 Unauthorized`（错误信息如：`invalid_token`）。  
2. **获取用户信息**：从主站用户库中提取当前令牌关联的用户数据（`id`、`name`、`email`）。  

#### 成功响应（JSON）  
```json
{
  "id": "USER_123456",                // 主站用户唯一标识（如数据库主键或 UUID）
  "name": "张三",                     // 用户姓名（主站存储的昵称或全称）
  "email": "zhangsan@a.com"           // 用户邮箱（主站注册邮箱）
}
```

---

## 三、整体交互时序图（简化版）  
```plaintext
b.a.com前端          b.a.com后端          a.com（本文档核心）
   │                   │                   │
   ├─1. 跳转至 GET a.com/api/sso/authorize?client_id=xxx&redirect_uri=yyy&scope=zzz │
   │                   │                   ├─2. 验证client_id/redirect_uri → 生成code
   │                   │                   └─3. 重定向至 redirect_uri?code=AUTH_CODE
   │                   ├─4. 接收code，发起 POST /api/sso/token（携带client_id/secret/code）
   │                   │                   ├─5. 验证client_id/secret/code → 返回access_token
   │                   └─6. 发起 POST /api/sso/user（携带access_token）
   │                   │                   └─7. 验证access_token → 返回用户信息{id,name,email}
   └─8. 自动登录/绑定本地用户
```

---

## 四、安全增强建议  
1. **`client_secret` 保护**：`b.a.com` 需将 `client_secret` 存储于服务端安全配置（如环境变量/密钥管理系统），禁止前端暴露。  
2. **HTTPS 强制**：所有接口必须通过 HTTPS 访问，防止中间人攻击。  
3. **令牌加密**：`access_token` 建议使用 JWT 并签名，或采用随机字符串+服务端缓存验证，避免泄露用户信息。  
4. **防重放攻击**：`code` 需标记为已使用，防止重复兑换 `access_token`。  

--- 

本方案明确了 `a.com` 需实现的三大核心接口及交互逻辑，适配 `b.a.com` 已完成的回调与鉴权逻辑，确保 SSO 流程的安全性与可靠性。


---
# 简化文档

主站 `a.com` 需实现以下 **3个核心接口**，适配子站点 `b.a.com` 的SSO集成：  


### 1. **SSO授权接口**  
- **方法**：`GET`  
- **路径**：`/api/sso/authorize`  
- **核心功能**：接收 `b.a.com` 的登录请求（携带 `client_id`、`redirect_uri` 等参数），验证合法性后，引导用户完成主站登录，最终重定向回 `b.a.com` 的回调地址（`http://b.a.com/api/oauth/redirect/sso`）并附带授权码 `code`（如 `?code=AUTH_CODE_XXX`）。示例：`http://b.a.com/api/oauth/redirect/sso?code=AUTH_CODE_XXX`


### 2. **访问令牌接口**  
- **方法**：`POST`  
- **路径**：`/api/sso/token`  
- **核心功能**：接收 `b.a.com` 后端的 `code` 兑换请求（携带 `client_id`、`client_secret`、`code`），验证通过后返回访问令牌 `access_token`（JSON格式，至少包含含 `access_token`、`token_type`、`scope`）。  


### 3. **用户信息接口**  
- **方法**：`POST`  
- **路径**：`/api/sso/user`  
- **核心功能**：接收 `b.a.com` 后端的用户信息请求（通过 `Authorization: Bearer <access_token>` 或请求体传递 `access_token`），验证令牌后返回用户数据（JSON格式，至少包含 `id`、`name`、`email`）。  


**总结**：3个接口分别负责「授权码发放」「令牌兑换」「用户信息同步」，支撑 `b.a.com` 完成SSO登录与用户绑定。