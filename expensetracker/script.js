// Setup Three.js scene for smooth infinite background animation
const container = document.getElementById('background-3d');
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
renderer.setSize(window.innerWidth, window.innerHeight);
container.appendChild(renderer.domElement);
camera.position.z = 30;

// Create array of geometries to randomly assign
const geometries = [new THREE.IcosahedronGeometry(3, 0), new THREE.BoxGeometry(4, 4, 4), new THREE.TetrahedronGeometry(3, 0)];
// Soft material with transparency and metalness for smooth shading and moderate opacity
const material = new THREE.MeshStandardMaterial({
  color: 0x557a95,
  metalness: 0.5,
  roughness: 0.4,
  transparent: true,
  opacity: 0.15,
});

const shapes = [];
const shapeCount = 15;
for(let i = 0; i < shapeCount; i++) {
  const geometry = geometries[i % geometries.length];
  const mesh = new THREE.Mesh(geometry, material);

  // Distribute shapes within radius sphere for even spread
  function randomPosition(radius) {
    const u = Math.random();
    const v = Math.random();
    const theta = 2 * Math.PI * u;
    const phi = Math.acos(2 * v - 1);
    const r = radius * Math.cbrt(Math.random());
    return {
      x: r * Math.sin(phi) * Math.cos(theta),
      y: r * Math.sin(phi) * Math.sin(theta),
      z: r * Math.cos(phi),
    };
  }

  const pos = randomPosition(25);
  mesh.position.set(pos.x, pos.y, pos.z);

  // Random slow rotation speeds
  mesh.rotationSpeed = {
    x: (Math.random() - 0.5) * 0.0015,
    y: (Math.random() - 0.5) * 0.0015,
    z: (Math.random() - 0.5) * 0.0015,
  };

  shapes.push(mesh);
  scene.add(mesh);
}

// Lighting with ambient + subtle directional for depth
const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
scene.add(ambientLight);

const directionalLight = new THREE.DirectionalLight(0xffffff, 0.3);
directionalLight.position.set(10, 10, 10);
scene.add(directionalLight);

// Animate loop - rotate shapes smoothly
function animate() {
  requestAnimationFrame(animate);
  shapes.forEach(shape => {
    shape.rotation.x += shape.rotationSpeed.x;
    shape.rotation.y += shape.rotationSpeed.y;
    shape.rotation.z += shape.rotationSpeed.z;
  });
  renderer.render(scene, camera);
}

animate();

// Responsive resize handler
window.addEventListener('resize', () => {
  camera.aspect = window.innerWidth/window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
});


function validateForm() {
  const username = document.getElementById("username").value.trim();
  const email = document.getElementById("email").value.trim();
  const phone = document.getElementById("phone").value.trim();
  const gender = document.getElementById("gender").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const expenseName = document.getElementById("expenseName").value.trim();
  const amount = document.getElementById("amount").value.trim();
  const category = document.getElementById("category").value;
  const date = document.getElementById("date").value;

  if (!username || !email || !phone || !gender || !password || !confirmPassword || !expenseName || !amount || !category || !date) {
    alert("Please fill all the fields.");
    return false;
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    alert("Enter a valid email address.");
    return false;
  }

  const phonePattern = /^\d{10}$/;
  if (!phonePattern.test(phone)) {
    alert("Phone number must be exactly 10 digits.");
    return false;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match.");
    return false;
  }

  return true;
}

function validateContactForm() {
  const name = document.getElementById("contactName").value.trim();
  const email = document.getElementById("contactEmail").value.trim();
  const message = document.getElementById("contactMessage").value.trim();

  if (!name || !email || !message) {
    alert("Please fill all contact form fields.");
    return false;
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    alert("Enter a valid email address.");
    return false;
  }

  return true;
}
// script.js
// Protect pages by checking PHP session via an endpoint
async function checkAuth() {
  const resp = await fetch('auth_check.php'); // returns 200 if session exists, 401 if not
  if (resp.status === 401) {
    window.location.href = 'login.php';
  }
}
document.addEventListener('DOMContentLoaded', checkAuth);
