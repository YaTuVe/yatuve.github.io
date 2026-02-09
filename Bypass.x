// Bypass.x - Anti-detection
@interface AntiCheatBypass : NSObject
@end

@implementation AntiCheatBypass

+ (void)load {
    // Hook de funciones de detección comunes
    MSHookFunction((void *)dlsym(RTLD_DEFAULT, "dladdr"),
                   (void *)hook_dladdr,
                   (void **)&original_dladdr);
    
    MSHookFunction((void *)dlsym(RTLD_DEFAULT, "dlsym"),
                   (void *)hook_dlsym,
                   (void **)&original_dlsym);
    
    // Ocultar tweak
    const char *hideLibraries[] = {"libsubstrate.dylib", "CydiaSubstrate", "YATUVE.dylib", NULL};
    for (int i = 0; hideLibraries[i] != NULL; i++) {
        MSHookFunction((void *)dlsym(RTLD_DEFAULT, "dlopen"),
                       (void *)hook_dlopen,
                       (void **)&original_dlopen);
    }
}

// Hook dladdr para ocultar direcciones
int (*original_dladdr)(const void *addr, Dl_info *info);
int hook_dladdr(const void *addr, Dl_info *info) {
    int result = original_dladdr(addr, info);
    if (result && info->dli_fname) {
        NSString *fname = [NSString stringWithUTF8String:info->dli_fname];
        if ([fname containsString:@"YATUVE"] || [fname containsString:@"Cydia"]) {
            info->dli_fname = "/usr/lib/libSystem.B.dylib";
        }
    }
    return result;
}

@end