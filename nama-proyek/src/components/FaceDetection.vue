<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import * as faceapi from 'face-api.js';

const videoRef = ref<HTMLVideoElement | null>(null);
const canvasRef = ref<HTMLCanvasElement | null>(null);
const expressionText = ref<string>("Menunggu deteksi...");

let interval: number | null = null;

onMounted(async () => {
  await loadModels();
  startCamera();
});

onUnmounted(() => {
  stopCamera();
});

const loadModels = async (): Promise<void> => {
  console.log("Memuat model...");
  await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/');
await faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/');
await faceapi.nets.faceExpressionNet.loadFromUri('https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/');

  console.log("Model berhasil dimuat!");
};

const startCamera = async (): Promise<void> => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
    if (videoRef.value) {
      videoRef.value.srcObject = stream;
    }

    interval = window.setInterval(async () => {
      await detectFaces();
    }, 100);
  } catch (error) {
    console.error("Gagal mengakses kamera:", error);
  }
};

const stopCamera = (): void => {
  if (interval !== null) {
    clearInterval(interval);
  }
  if (videoRef.value?.srcObject) {
    let tracks = (videoRef.value.srcObject as MediaStream).getTracks();
    tracks.forEach(track => track.stop());
  }
};

const detectFaces = async (): Promise<void> => {
  if (!videoRef.value) return;

  const detections = await faceapi.detectAllFaces(videoRef.value, new faceapi.TinyFaceDetectorOptions())
    .withFaceLandmarks()
    .withFaceExpressions();

  drawCanvas(detections);
  updateExpression(detections);
};

const drawCanvas = (detections: faceapi.WithFaceExpressions<faceapi.WithFaceLandmarks<faceapi.FaceDetection>>[]): void => {
  if (!canvasRef.value || !videoRef.value) return;

  const canvas = canvasRef.value;
  const ctx = canvas.getContext("2d");

  if (!ctx) return;

  canvas.width = videoRef.value.videoWidth;
  canvas.height = videoRef.value.videoHeight;
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  detections.forEach(detection => {
    const { x, y, width, height } = detection.detection.box;
    ctx.strokeStyle = "red";
    ctx.lineWidth = 2;
    ctx.strokeRect(x, y, width, height);
  });
};

const updateExpression = (detections: faceapi.WithFaceExpressions<faceapi.WithFaceLandmarks<faceapi.FaceDetection>>[]): void => {
  if (detections.length > 0) {
    const expressions = detections[0].expressions;
    const topExpression = Object.entries(expressions).sort((a, b) => b[1] - a[1])[0][0];
    expressionText.value = `Ekspresi: ${topExpression}`;
  } else {
    expressionText.value = "Tidak ada wajah terdeteksi.";
  }
};
</script>

<template>
  <div class="face-detection">
    <h1>Deteksi Wajah dan Ekspresi</h1>
    <video ref="videoRef" autoplay></video>
    <canvas ref="canvasRef"></canvas>
    <p class="expression-text">{{ expressionText }}</p>
  </div>
</template>

<style scoped>
.face-detection {
  position: relative;
  width: 100%;
  max-width: 640px;
  text-align: center;
  margin: 0 auto;
}

video, canvas {
  width: 100%;
  height: auto;
  position: absolute;
  top: 0;
  left: 0;
}

.expression-text {
  margin-top: 10px;
  font-size: 20px;
  font-weight: bold;
  color: #ff4c4c;
}
</style>
