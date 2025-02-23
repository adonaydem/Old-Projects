

import numpy as np
import matplotlib.pyplot as plt
import os
import math
import shutil
import glob


def train():
  root = "./content/brain_tumor_dataset"
  num_img = {}

  for dir in os.listdir(root):
    num_img[dir] = len(os.listdir(os.path.join(root, dir)))

  
  def split_data(p, split):
    if not os.path.exists("./"+p):
      os.mkdir("./"+p)

      for dir in os.listdir(root):
        os.makedirs("./"+p+"/"+dir)
        for img in np.random.choice(a = os.listdir(os.path.join(root, dir)), size=(math.floor(split*num_img[dir])-2), replace=False):
          O = os.path.join(root, dir, img)
          D = os.path.join("./"+p, dir)
          shutil.copy(O, D)
          os.remove(O)

  split_data("val", 0.15)
  split_data("test", 0.15)
  split_data("train", 0.7)


  from keras.layers import Conv2D, MaxPool2D, Dropout, Flatten, BatchNormalization, GlobalAvgPool2D, Dense
  from keras.models import Sequential
  from tensorflow.keras.utils import load_img
  from keras.preprocessing.image import ImageDataGenerator
  import keras

  model = Sequential()

  model.add(Conv2D(filters=16, kernel_size=(3,3), activation='relu', input_shape=(224,224,3)))

  model.add(Conv2D(filters=36, kernel_size=(3,3), activation='relu'))

  model.add(MaxPool2D(pool_size=(2,2)))

  model.add(Conv2D(filters=64, kernel_size=(3,3), activation='relu'))

  model.add(MaxPool2D(pool_size=(2,2)))

  model.add(Conv2D(filters=128, kernel_size=(3,3), activation='relu'))

  model.add(MaxPool2D(pool_size=(2,2)))

  model.add(Dropout(rate=0.15))

  model.add(Flatten())

  model.add(Dense(units=64, activation='relu'))

  model.add(Dropout(rate=0.15))

  model.add(Dense(units=1, activation='sigmoid'))


  model.summary()

  model.compile(optimizer='adam', loss=keras.losses.binary_crossentropy, metrics=['accuracy'])

  def preprocess(path, type):
    if type==1:
      image_data = ImageDataGenerator(zoom_range=0.2, shear_range=0.2, rescale=1/255, horizontal_flip=True)
    elif type==2:
      image_data = ImageDataGenerator(rescale=1/255)

    image = image_data.flow_from_directory(directory=path, target_size=(224,224), batch_size=32, class_mode='binary')

    return image

  path = "./train"
  train_data = preprocess(path, 1)
  path = "./test"
  test_data = preprocess(path, 2)
  path = "./val"
  val_data = preprocess(path, 2)

  from keras.callbacks import ModelCheckpoint, EarlyStopping

  es= EarlyStopping(monitor="val_accuracy", min_delta=0.01, patience=10,verbose=1,mode='auto')

  mc = ModelCheckpoint(monitor="val_accuracy", filepath="./bestmodel.h5", verbose=1, save_best_only=True, mode='auto')

  cb = [es,mc]

  def data_generator(data):
      while True:
          for batch in data:
              yield batch

  # Create data generators for training and validation
  train_generator = data_generator(train_data)
  val_generator = data_generator(val_data)
  hs = model.fit_generator(generator=train_generator, steps_per_epoch=8, epochs=30, verbose=1, validation_data=val_generator, validation_steps=16, callbacks=cb)

  acc = model.evaluate_generator(test_data)[1]

  print("testing accuracy: ", acc)
