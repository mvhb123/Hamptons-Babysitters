//
//  NSMutableDictionary+Safe.h
//  SMCoreComponent
//
//  Created by sofmen on 21/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSMutableDictionary (Safe)

- (void)setSafeObject:(id)nonNullObject forKey:(id<NSCopying>)key;

- (void)setSafeValue:(id)value forKey:(NSString *)key;

- (id)safeObjectForKey:(id)aKey;

@end
