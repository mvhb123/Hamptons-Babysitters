//
//  ImageCache.m
//  Canogle
//
//  Created by Tushar Sarkar on 10/19/11.
//  Copyright 2011 Sofmen Inc. All rights reserved.
//

#import "ImageCache.h"
#import "ImageCacheObject.h"

@implementation ImageCache

@synthesize totalSize;

-(id)initWithMaxSize:(NSUInteger) max  {
    if (self = [super init]) {
        totalSize = 0;
        maxSize = max;
        myDictionary = [[NSMutableDictionary alloc] init];
    }
    return self;
}


-(void)insertImage:(UIImage*)image withSize:(NSUInteger)sz forKey:(NSString*)key{
    ImageCacheObject *object = [[ImageCacheObject alloc] initWithSize:sz Image:image];
    while (totalSize + sz > maxSize) {
        NSDate *oldestTime;
        NSString *oldestKey;
	@try {
		for (NSString *key in [myDictionary allKeys]) {
			ImageCacheObject *obj = [myDictionary objectForKey:key];
			if(oldestTime != nil &&(![obj.timeStamp isKindOfClass:[NSDate class]] || ![oldestTime isKindOfClass:[NSDate class]])){
				
                return;
			}
			if (oldestTime == nil || [obj.timeStamp compare:oldestTime] == NSOrderedAscending) {
				oldestTime = obj.timeStamp;
				oldestKey = key;
			}
		}
		if (oldestKey == nil) 
			break; // shoudn't happen
		ImageCacheObject *obj = [myDictionary objectForKey:oldestKey];
		totalSize -= obj.size;
		[myDictionary removeObjectForKey:oldestKey];
	}
	@catch (NSException * e) {
		//NSLog(@"Exception: %@",[e description]);
       // break;
	}@finally {
		//break;
        break;
	}	
        
    }
    [myDictionary setSafeObject:object forKey:key];
}

-(UIImage*)imageForKey:(NSString*)key {
    ImageCacheObject *object = [myDictionary objectForKey:key];
    if (object == nil)
        return nil;
    [object resetTimeStamp];
    return object.image;
}

@end
