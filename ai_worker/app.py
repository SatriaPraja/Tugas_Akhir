from flask import Flask, request, jsonify
import joblib
import pandas as pd

app = Flask(__name__)

# Load model dan scaler
try:
    model = joblib.load('kmeans_model.pkl')
    scaler = joblib.load('scaler.pkl')
    print("Model & Scaler loaded successfully!")
except Exception as e:
    print(f"Error loading files: {e}")

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data_request = request.json['data'] 
        df = pd.DataFrame(data_request)
        
        # Urutan kolom harus sama dengan saat training
        features = ['luas', 'produktivitas', 'jenis_tanah']
        X = df[features]
        
        # Transformasi & Prediksi
        X_scaled = scaler.transform(X)
        clusters = model.predict(X_scaled)
        
        # Kembalikan hasil (Cluster + 1 agar mulai dari 1)
        return jsonify({
            'status': 'success',
            'clusters': (clusters + 1).tolist()
        })
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 400

if __name__ == '__main__':
    # Berjalan di port 5000
    app.run(host='127.0.0.1', port=5000, debug=True)