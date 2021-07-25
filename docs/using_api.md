# Using API

To get notices (the content of the bubble), you need to follow 4 steps:

1. GET
`https://api.dismoi.io/api/v3/matching-contexts`

You can check the response of matching contexts [here](https://github.com/dis-moi/backend/blob/master/docs/openapi.json#L12).

2. Check if a matching context match current URL. You can find an example [here](https://framagit.org/bequet/bequet/-/blob/master/background.js#L32) on how it is done.

3. Get notice id of the matching context. Example [here](https://framagit.org/bequet/bequet/-/blob/master/background.js#L37).

4. GET
`https://api.dismoi.io/api/v3/notices/${noticeId}`

You can check the response of the notice [here](https://github.com/dis-moi/backend/blob/master/docs/openapi.json#L126).
