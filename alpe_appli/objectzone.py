import kivy
kivy.require('1.0.6')

from glob import glob
from random import randint
from os.path import join, dirname
from kivy.app import App
from kivy.logger import Logger
from kivy.uix.scatter import Scatter
from kivy.uix.image import Image
from kivy.properties import StringProperty, ObjectProperty, NumericProperty
# FIXME this shouldn't be necessary
from kivy.core.window import Window
from kivy.clock import *


class ObjectZone(Image):
    '''Picture is the class that will show the image with a white border and a
    shadow. They are nothing here because almost everything is inside the
    picture.kv. Check the rule named <Picture> inside the file, and you'll see
    how the Picture() is really constructed and used.

    The source property will be the filename to show.

    '''
    source = StringProperty(None)
    idx_zone = ObjectProperty(None)     
    object_id = NumericProperty(0)

    def __init__(self, **kwargs):
        self._touches = []

        super(ObjectZone, self).__init__(**kwargs)

    def on_touch_down(self, touch):
        if not self.collide_point(*touch.pos):
            return

        touch.grab(self)
        self._touches.append(touch)
        Clock.unschedule(self.analyse_object)
        Clock.schedule_once((self.analyse_object),2)

        return True

    # def on_touch_move(self, touch):
    #     pass

    def on_touch_up(self,touch):
        if touch in self._touches and touch.grab_state:
            touch.ungrab(self)
            # del self._last_touch_pos[touch]
            self._touches.remove(touch)

 
        # stop propagating if its within our bounds
        # if self.collide_point(x, y):
        #     return True
    def analyse_object(self,touch):
        print "Nombre de pied : " + str(len(self._touches)) + " : " + str(self.idx_zone)
        self.object_id = int(len(self._touches))