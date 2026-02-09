ARCHS = arm64 arm64e
TARGET = iphone:clang:latest:latest
INSTALL_TARGET_PROCESSES = SpringBoard

include $(THEOS)/makefiles/common.mk

TWEAK_NAME = YATUVE

YATUVE_FILES = Tweak.x
YATUVE_CFLAGS = -fobjc-arc

include $(THEOS_MAKE_PATH)/tweak.mk