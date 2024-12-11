let signalingServer;
let localStream;
let peerConnection;
const configuration = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };
const roomId = new URLSearchParams(window.location.search).get("room") || "default";

function connectWebSocket() {
    signalingServer = new WebSocket(`ws://localhost:8080/signaling?roomId=${roomId}`);

    signalingServer.onopen = () => {
        console.log("Conexión establecida con el servidor de señalización.");
    };

    signalingServer.onerror = (error) => {
        console.error("Error en WebSocket:", error);
        setTimeout(connectWebSocket, 1000);
    };

    signalingServer.onclose = () => {
        console.warn("Conexión cerrada. Intentando reconectar...");
        setTimeout(connectWebSocket, 1000);
    };

    signalingServer.onmessage = async (message) => {
        const data = JSON.parse(message.data);
        console.log("Mensaje recibido:", data);

        try {
            if (data.type === 'offer') {
                if (!data.sdp || typeof data.sdp !== 'string') {
                    throw new Error("SDP de la oferta no es válido.");
                }
                await handleOffer(data.sdp);
            } else if (data.type === 'answer') {
                if (!data.sdp || typeof data.sdp !== 'string') {
                    throw new Error("SDP de la respuesta no es válido.");
                }
                await peerConnection.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp: data.sdp }));
            } else if (data.type === 'candidate') {
                if (peerConnection.remoteDescription) {
                    await peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
                } else {
                    console.warn("Candidato ICE recibido antes de establecer la descripción remota. Ignorando...");
                }
            }
        } catch (error) {
            console.error("Error al procesar el mensaje:", error);
        }
    };
}

function sendMessage(data) {
    if (signalingServer.readyState === WebSocket.OPEN) {
        signalingServer.send(JSON.stringify(data));
    } else {
        console.warn("WebSocket no está listo. Intentando reenviar...");
        setTimeout(() => sendMessage(data), 100);
    }
}

async function startConnection() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        document.getElementById('localVideo').srcObject = localStream;

        peerConnection = new RTCPeerConnection(configuration);
        localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

        peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                sendMessage({ type: 'candidate', candidate: event.candidate, roomId: roomId });
            }
        };

        peerConnection.ontrack = (event) => {
            document.getElementById('remoteVideo').srcObject = event.streams[0];
        };

        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        sendMessage({ type: 'offer', sdp: offer.sdp, roomId: roomId });

        console.log("Oferta enviada:", offer);
    } catch (error) {
        console.error("Error al iniciar la conexión:", error);
    }
}

async function handleOffer(sdp) {
    try {
        if (!sdp || typeof sdp !== 'string') {
            throw new Error("SDP inválido.");
        }
        console.log("Oferta recibida. Creando respuesta...");
        await peerConnection.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp }));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        sendMessage({ type: 'answer', sdp: answer.sdp, roomId: roomId });
        console.log("Respuesta enviada:", answer);
    } catch (error) {
        console.error("Error al manejar la oferta:", error);
    }
}

document.getElementById('startButton').addEventListener('click', startConnection);
connectWebSocket();
