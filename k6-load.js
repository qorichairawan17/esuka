import http from "k6/http";
import { check, sleep } from "k6";

export const options = {
    stages: [
        { duration: "10s", target: 200 }, // naik dari 0 ke 200 user dalam 10 detik
        { duration: "30s", target: 200 }, // tahan 200 user selama 30 detik
        { duration: "10s", target: 0 }, // turunkan kembali ke 0 user
    ],
    thresholds: {
        http_req_duration: ["p(95)<800"], // 95% request harus selesai < 800ms
        http_req_failed: ["rate<0.01"], // error rate < 1%
    },
};

export default function () {
    const res = http.get("http://esuka.test/"); // ubah sesuai alamat

    check(res, {
        "status 200": (r) => r.status === 200,
        "body mengandung Laravel": (r) => r.body.includes("Laravel"),
    });

    sleep(1); // jeda 1 detik antar request (simulasi user nyata)
}
