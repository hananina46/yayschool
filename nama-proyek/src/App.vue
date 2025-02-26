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
  <div class="container">
    <h1>Deteksi Wajah dan Ekspresi</h1>
    <p class="expression-text">Ekspresi: {{ expressionText }}</p>

    <div class="face-detection">
      <video ref="videoRef" autoplay playsinline></video>
      <canvas ref="canvasRef"></canvas>
    </div>
  </div>
</template>

<style scoped>
/* Container utama */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  max-width: 800px;
  margin: 20px auto;
  padding: 10px;
  text-align: center;
}

/* Video dan Canvas Wrapper */
.face-detection {
  position: relative;
  width: 100%;
  max-width: 640px;
  aspect-ratio: 16 / 9;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Video dan Canvas Styling */
video, canvas {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  object-fit: cover;
}

/* Teks Ekspresi */
.expression-text {
  margin-top: 15px;
  font-size: 18px;
  font-weight: bold;
  color: #ff4c4c;
  background: #fff;
  padding: 10px 15px;
  border-radius: 5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Responsif untuk HP */
@media (max-width: 768px) {
  .face-detection {
    max-width: 100%;
    aspect-ratio: 4 / 3;
  }

  .expression-text {
    font-size: 16px;
    padding: 8px 12px;
  }
}

@media (max-width: 480px) {
  .expression-text {
    font-size: 14px;
    padding: 6px 10px;
  }
}
</style>
