# Y-Care - Yakult

---

Thông qua điện thoại thông tin, vòng đeo tay,.. việc đọc các chỉ số sức khoẻ rất đơn giản và
nhanh chóng. Vì vậy thật dễ dàng theo dõi sức khoẻ người thân thông qua ứng dụng.

## Làm việc với docker

*Lệnh build image*
```shell
docker build -t zcr.rshcm.com/wheel/api:<version> . --build-arg ENV=production --no-cache
```

*Push image lên Private Hub*
```shell
docker image push zcr.rshcm.com/wheel/api:<version>
```

## Triển khai môi trường

*Tạo Namespace*
```shell
ENV=production envsubst < 1.namespace.yaml | kubectl apply -f -
```

*Tạo GlusterFS Endpoint*
```shell
ENV=production envsubst < 2.glusterfs-endpoint.yaml | kubectl apply -f -
```
- GlusterFS tạo sẵn volume `wheel-production`

*Cài đặt MariaDB Endpoint*

```shell
ENV=production envsubst < 3.mariadb-endpoint.yaml | kubectl apply -f -
```

*Cài đặt Redis Cluster*
```shell
kubectl create configmap --namespace=wheel-production redis-cluster-scripts \
  --from-file=scripts/redis-cluster/ping_liveness_local.sh \
  --from-file=scripts/redis-cluster/ping_readiness_local.sh
```

```shell
kubectl create configmap --namespace=wheel-production redis-cluster-env --from-env-file=.redis-cluster.env
```

```shell
ENV=production envsubst < 4.redis-cluster.yaml | kubectl apply -f -
```

*Tạo giấy phép truy cập Private Registry*
```shell
kubectl create secret --namespace=wheel-production generic private-registry-credential \
  --from-file=.dockerconfigjson=/root/.docker/config.json \
  --type=kubernetes.io/dockerconfigjson
```

## Triển khai source code

### Triển khai mới

*Tạo Config Map từ .env file*
```shell
kubectl create configmap --namespace=wheel-production wheel-api-env --from-env-file=.wheel-api.env
```

*Triển khai dịch vụ*
```shell
ENV=production VER=<version> envsubst < 5.wheel-api.yaml | kubectl apply -f -
```
### Cập nhật/ phục hồi phiên bản

*Cập nhật phiên bản*
```shell
kubectl set image --namespace=wheel-production deployment/wheel-api wheel-api-pod=zcr.rshcm.com/wheel/api:<new-version>
```

*Phục hồi phiên bản trước (nếu cập nhật lỗi)*
```shell
kubectl rollout undo --namespace=wheel-production deployment/wheel-api
```
