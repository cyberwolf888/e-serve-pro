# Base Image

`Dockerfile` supplies the stable FrankenPHP runtime and PHP extensions used by the application image.

Publish the current version once before deploying the application image:

```sh
docker login
docker build -t xyndr0me/pro-bi-smart-base:0.1.0 .docker/baseimage
docker tag xyndr0me/pro-bi-smart-base:0.1.0 xyndr0me/pro-bi-smart-base:latest
docker push xyndr0me/pro-bi-smart-base:0.1.0
docker push xyndr0me/pro-bi-smart-base:latest
```

For base-image changes, increment `VERSION`. Trigger the GitHub Actions workflow manually to publish the version and update `latest`. It rejects an existing version tag.
