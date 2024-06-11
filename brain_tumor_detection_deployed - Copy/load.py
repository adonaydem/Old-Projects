from keras.models import load_model
import numpy as np
def load(img):
    model = load_model("./bestmodel.h5")


    import tensorflow as tf

    image_bytes = img.read()
    image = tf.image.decode_image(image_bytes, channels=3)
    image = tf.image.resize(image, [224, 224])
    input_arr = tf.keras.utils.img_to_array(image)/255
    input_arr = np.expand_dims(input_arr, axis=0)
    pred_prob = model.predict(input_arr)[0]
    
    return pred_prob