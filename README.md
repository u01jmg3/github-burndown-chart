## Config

- In `index.html` change the following variables to suit your needs:

```js
var owner = '';
var repo = '';
var accessToken = '';
```

- `owner` is the lowercase name of the user or organization who the repository belongs to
- `repo` is the lowercase name of the repository you want to produce a burndown chart for
- `accessToken` can be generated from:
  - https://github.com/settings/applications > Personal access tokens
    - Your token only needs the `repo` scope to be ticked

## Example

![01](https://github.com/u01jmg3/github-burndown-chart/raw/master/test/example.png)