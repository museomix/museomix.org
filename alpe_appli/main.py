import kivy
kivy.require('1.0.6')

from glob import glob
from random import randint
from os.path import join, dirname
from kivy.app import App
from kivy.logger import Logger
from kivy.uix.scatter import Scatter
from kivy.properties import StringProperty, ListProperty, NumericProperty, BooleanProperty, ObjectProperty
from kivy.core.window import Window
from kivy.graphics.transformation import Matrix
from kivy.uix.image import Image
from kivy.uix.widget import Widget
from kivy.uix.floatlayout import FloatLayout
from kivy.clock import Clock
from kivy.core.window import Window
from kivy.core.image import Image as CoreImage
from kivy.animation import Animation
from kivy.uix.button import Button
from objectzone import *
from kivy.core.audio import SoundLoader
import math

#*****************************************************#
#********************** FARMER ***********************#
#*****************************************************#

class Scythe(Widget):
    life = NumericProperty(0.0)    

class Harvest(Scatter):
    filename = StringProperty(None)
    sizeX = NumericProperty(0.0)
    sizeY = NumericProperty(0.0)
    line = NumericProperty(0.0)
    timerStart = BooleanProperty(False)
    scythe = ObjectProperty(None)
    gm = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(Harvest, self).__init__(**kwargs)
        self.inputs = []
        for i in range(int(self.sizeX)):
            for y in range(int(self.sizeY)):
                try:
                    tmp = Image(source=self.filename, pos=(i * 30, y * 30))
                    self.add_widget(tmp)
                except Exception, e:
                    Logger.exception('Harvest: Unable to load <%s>' % self.filename)
        self.scythe = Scythe(life=(1.0 - self.line / self.sizeY))
        self.add_widget(self.scythe)
            
    def cut_rye(self, dt):
        if  self.scythe.life > 0.0:
            Clock.unschedule(self.cut_rye)
            self.timerStart = False
        count = 0
        check = False
        children = self.children[:]
        children.reverse()
        for child in children:
            if count > self.sizeY * self.line - 1 and count < self.sizeY * self.line + self.sizeY:
                check = True
                child.source = 'images/epi_cueilli.png'
            count += 1
        if check:
            self.scythe.life = 1 - self.line / self.sizeY * 1.6
            self.line += 1
        
    def on_touch_down(self, touch):
        x, y = touch.x, touch.y
        if self.collide_point(x, y) and len(self.inputs) == 0:
            self.inputs.append(touch)

    def on_touch_move(self, touch):        
        x, y = touch.x, touch.y
        if self.collide_point(x, y) and touch in self.inputs and self.scythe.life > 0.0:
            if not self.timerStart:
                self.timerStart = True
                Clock.schedule_interval(self.cut_rye, 0.5)
        elif self.scythe.life <= 0.0:
            self.gm.restartFarmerManager()
        
    def on_touch_up(self, touch):
        x, y = touch.x, touch.y
        if self.collide_point(x, y) and touch in self.inputs:
            self.inputs.remove(touch)
            Clock.unschedule(self.cut_rye)
            self.timerStart = False

#*****************************************************#
#********************** MAZE *************************#
#*****************************************************#

class Sheep(Scatter):
    image = ObjectProperty(None)
    canMove = BooleanProperty(False)
    idx_frame = NumericProperty(0.0)
    previous_position = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(Sheep, self).__init__(**kwargs)
        self.image = CoreImage('images/maze.png', keep_data=True)
        self.previous_position = self.pos

    def on_touch_down(self, touch):
        if not self.canMove:
            return
        ret = super(Sheep, self).on_touch_down(touch)
        if not ret:
            return
        return True

    def on_touch_move(self, touch):
        if not self.canMove:
            return
        x, y = touch.pos
        if not self or not self.parent or not self.parent.maze:
            return
        mazeimg = self.parent.maze
        x -= mazeimg.x
        y -= mazeimg.y
        x = int(x)
        y = int(y)
        y = self.parent.maze.height - y
        try:
            color = self.image.read_pixel(x, y)
        except IndexError:
            return
        if color[-1] == 0:
            return
        if self.idx_frame == 10 :   

            if touch.x != self.previous_position[0]:
                angle = math.degrees(math.atan((self.previous_position[1]-touch.y)/(self.previous_position[0]-touch.x)))
                if self.previous_position[0] > touch.x :
                    angle += 180
               
            else:
                angle = 90 * math.copysign(1, self.rotation)
            self.rotation = angle 
            self.previous_position = touch.pos
            self.idx_frame=0
        else :
            self.idx_frame+=1
        #print angle

        # ret = super(Sheep, self).on_touch_move(touch)
        # return ret

        if abs(touch.x - self.center_x) < 50 and abs(touch.y - self.center_y) < 50:
            ret = super(Sheep, self).on_touch_move(touch)
            return ret
        return True

    def on_touch_up(self, touch):
        if not self.canMove:
            return
        if not touch.grab_current == self:
            return False
        ret = super(Sheep, self).on_touch_up(touch)
        return ret

class Maze(FloatLayout):
    sheep = ObjectProperty(None)
    posBegin = (1587, 615)
    #posBegin = (310, 615)
    posEnd = (310.0, 615.0)
    check = BooleanProperty(False)
    button = ObjectProperty(None)
    stick = BooleanProperty(False)
    bell = BooleanProperty(False)
    zoneBell = ObjectProperty(None)
    zoneStick = ObjectProperty(None)
    gm = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(Maze, self).__init__(**kwargs)
        self.sheep = Sheep(center=self.posBegin)
        self.add_widget(self.sheep)
        self.zoneBell = ObjectZone(source='images/TXT_BATON.png', pos=(50, 700), size=(413, 150), size_hint=(None, None), idx_zone=1)
        self.zoneBell.bind(object_id=self.checkDropZone)
        self.add_widget(self.zoneBell)

        self.zoneStick = ObjectZone(source='images/TXT_CLOCHE.png', pos=(50, 900), size=(413, 150), size_hint=(None, None), idx_zone=2)
        self.zoneStick.bind(object_id=self.checkDropZone)
        self.add_widget(self.zoneStick)

    def checkDropZone(self, instance, value):
        #True check is value == 5
        if value <= 5 and not self.bell:
            self.bell = True
            instance.source = 'images/drop_zone_VALID.png'
        #True check is value == 8
        elif value <= 8 and not self.stick:
            self.stick = True
            instance.source = 'images/drop_zone_VALID.png'
        if self.bell and self.stick:
            self.sheep.canMove = True
            self.remove_widget(self.zoneStick)
            self.remove_widget(self.zoneBell)

    def on_touch_move(self, touch):
        if not self.stick or not self.bell:
            return
        if self.sheep.canMove and self.sheep.center_x > self.posEnd[0] - 20 and self.sheep.center_x < self.posEnd[0] + 20 \
           and self.sheep.center_y > self.posEnd[1] - 20 and self.sheep.center_y < self.posEnd[1] + 20:
            self.check = True

    def on_touch_up(self, touch):
        if not self.stick or not self.bell:
            return
        if self.check:
            self.remove_widget(self.sheep)
                    
    def on_check(self, instance, value):
        sheepimg = Image(source='images/TXT_BRAVO_MOUTONS.png', size_hint=(None, None), size=(300, 150))
        self.add_widget(sheepimg)
        sheepimg.pos = (300, 300)

        self.button = BtnOk(scenario=self)
        self.button.center = (630, 270)
        self.button.button.background_normal = 'images/BUT_OK_UNVALID.png'
        self.button.button.background_down = 'images/BUT_OK_VALID.png'
        self.add_widget(self.button)
                
    def start(self):
        self.parent.startWolf()
        
#*****************************************************#
#****************** WOLFATTACK ***********************#
#*****************************************************#

class Dog(Image):
    dogCollar = BooleanProperty(False)

    def __init__(self, **kwargs):
        super(Dog, self).__init__(**kwargs)

    def equip(self):
        self.dogCollar = True
        self.source = 'images/screenshot_troupeau_chien_collier.png'

class Wolf(Scatter):
    def __init__(self, **kwargs):
        super(Wolf, self).__init__(**kwargs)

class WolfAttack(FloatLayout):
    wolf = ObjectProperty(None)
    dog = ObjectProperty(None)
    info = ObjectProperty(None)
    collardog = BooleanProperty(None)
    collarZone = ObjectProperty(None)
    gm = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(WolfAttack, self).__init__(**kwargs)
        self.dog = Dog()
        self.add_widget(self.dog)
        self.wolf = Wolf()
        self.wolf.pos = (0, -300)
        self.wolf.rotation += 90
        self.add_widget(self.wolf)

        self.collarZone = ObjectZone(source='images/drop_zone_UNVALID.png', pos=(50, 700), size=(300, 150), size_hint=(None, None), idx_zone=1)
        self.collarZone.bind(object_id=self.checkDropZone)
        self.add_widget(self.collarZone)

        self.info = Image(source='images/TXT_COLLIER.png', size_hint=(None, None), size=(300, 150))        
        self.info.pos = (500, 200)
        self.add_widget(self.info)

        anim = Animation(x=310, y=-300,t='in_elastic', d=12)
        anim.on_complete = self.startAttack
        anim.start(self.wolf)

    def checkDropZone(self, instance, value):
        if value == 3:
            self.collardog = True
            self.dog.equip()
            self.remove_widget(self.collarZone)
            instance.source = 'images/drop_zone_VALID.png'

    def startAttack(self, widget):
        self.remove_widget(self.collarZone)
        anim = Animation(x=310, y=300,t='in_out_circ', d=3)
        anim.on_complete = self.attack
        anim.start(self.wolf)
        
    def attack(self, widget):
        if not self.collardog:
            self.dog.source = 'images/screenshot_troupeau_chien_morts.png'
            self.wolf.wolf.source = 'images/loup_repu.png'
        self.wolf.rotation -= 180
        anim = Animation(x=310, y=-300, t='in_out_sine', d=3)
        anim.on_complete=self.killWolf
        anim.start(self.wolf)

    def killWolf(self, widget):
        self.remove_widget(self.wolf)
        self.remove_widget(self.info)
        sheemimg = ObjectProperty(None)
        if self.collardog:
            sheepimg = Image(source='images/TXT_BRAVO_MOUT_2.png', size_hint=(None, None), size=(300, 150))
        else:
            sheepimg = Image(source='images/TXT_FAIL.png', size_hint=(None, None), size=(300, 150))        
        sheepimg.pos = (300, 300)
        self.add_widget(sheepimg)
        self.button = BtnOk(scenario=self)
        self.button.center = (630, 270)
        self.button.button.background_normal = 'images/BUT_OK_UNVALID.png'
        self.button.button.background_down = 'images/BUT_OK_VALID.png'

        self.add_widget(self.button)
        tmp = Retry(wa=self)
        self.retry = BtnOk(scenario=tmp)
        self.retry.center = (470, 270)
        self.retry.button.background_normal = 'images/BUT_BACK_UNVALID.png'
        self.retry.button.background_down = 'images/BUT_BACK_VALID.png'
        self.add_widget(self.retry)
                
    def start(self):
        self.gm.restartHerdsManager()

class Retry(Widget):
    wa = ObjectProperty()

    def __init__(self, **kwargs):
        super(Retry, self).__init__(**kwargs)

    def start(self):
        if self.wa.parent:
            self.wa.parent.restartWolf()
        else:
            self.wa.start()

#*******************************************************************************#
#*************************      MANAGERS         *******************************#
#*******************************************************************************#

class BtnOk(Scatter):
    scenario = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(BtnOk, self).__init__(**kwargs)

    def activate(self, but):
        self.scenario.start()

class BtnBack(Scatter):
    gm = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(BtnBack, self).__init__(**kwargs)

    def start(self, but):
        self.gm.restart()
    

class LaunchScenario(Scatter):
    scenario = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(LaunchScenario, self).__init__(**kwargs)

    def activate(self, but):
        self.scenario.start()
    

class HerdsManManager(FloatLayout):
    gm = ObjectProperty(None)
    wolfattack = ObjectProperty(None)
    maze = ObjectProperty(None)
    button = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(HerdsManManager, self).__init__(**kwargs)
        self.button = LaunchScenario(scenario=self)
        self.button.center = (1920 / 2, 270)
        self.button.button.background_normal = 'images/START_MOUT_UNVALID.png'
        self.button.button.background_down = 'images/START_MOUT_VALID.png'
        self.add_widget(self.button)
        
    def start(self):
        self.remove_widget(self.button)
        self.maze = Maze(gm=self.gm)
        self.add_widget(self.maze)

    def startWolf(self):
        self.remove_widget(self.maze)
        self.wolfattack = WolfAttack(gm=self.gm)
        self.add_widget(self.wolfattack)

    def restartWolf(self):
        self.remove_widget(self.wolfattack)
        self.wolfattack = WolfAttack(gm=self.gm)
        self.add_widget(self.wolfattack)

class FarmerManager(FloatLayout):
    gm = ObjectProperty(None)
    harvest = ObjectProperty(None)
    button = ObjectProperty(None)
    back = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(FarmerManager, self).__init__(**kwargs) 
        self.button = LaunchScenario(scenario=self, rotation=-180)
        self.button.center = (1920 / 2, 810)
        self.button.button.background_normal = 'images/START_CH_UNVALID.png'
        self.button.button.background_down = 'images/START_RECOLTE_VALID.png'
        self.add_widget(self.button)

        self.back = BtnOk(scenario=self.gm)
        self.back.center = (100, 100)
        self.back.button.background_normal = 'images/BUT_BACK_UNVALID.png'
        self.back.button.background_down = 'images/BUT_BACK_VALID.png'
        self.add_widget(self.back)
        
    def start(self):
        self.remove_widget(self.button)
        self.harvest = Harvest(gm=self.gm, sizeX=15.0, sizeY=10.0, filename='images/epi.png', rotation=-180)
        self.harvest.center = (1300.0, 1000.0)
        self.add_widget(self.harvest)


class GameManager(Widget): 
    root = ObjectProperty(None)
    herdsManager = ObjectProperty(None)
    farmerManager = ObjectProperty(None)

    def __init__(self, **kwargs):
        super(GameManager, self).__init__(**kwargs)
        self.herdsManager = HerdsManManager(gm=self)
        self.root.add_widget(self.herdsManager)
        self.farmerManager = FarmerManager(gm=self)
        self.root.add_widget(self.farmerManager)

    def restartHerdsManager(self):
        print "restart"
        self.root.remove_widget(self.herdsManager)
        self.herdsManager = HerdsManManager(gm=self)
        self.root.add_widget(self.herdsManager)

    def restartFarmerManager(self):
        self.root.remove_widget(self.farmerManager)
        self.farmerManager = FarmerManager(gm=self)
        self.root.add_widget(self.farmerManager)

    def start(self):
        self.restartFarmerManager()
        self.restartHerdsManager()

class GlobalLayout(FloatLayout):
    pass

class AlpeApp(App):
    def build(self):
        root = GlobalLayout()
        game = GameManager(root=root)
        return root

if __name__ == '__main__':
    AlpeApp().run()

