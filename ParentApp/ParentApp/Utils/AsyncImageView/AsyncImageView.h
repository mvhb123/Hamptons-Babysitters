//
//  AsyncImageView.h
//  Canogle
//
//  Created by Tushar Sarkar on 10/19/11.
//  Copyright 2011 Sofmen Inc. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface AsyncImageView : UIView {
    NSURLConnection *connection;
    NSMutableData *data;
    NSString *urlString; // key for image cache dictionary
	NSString *klocalImageName;
	id delegate;
    SEL operation;
	NSSet *toucheEvents;
	BOOL isFromFeatureonNow;
    @public
    UIImageView *imageView;
    BOOL isBlur;
}
@property (nonatomic , assign)BOOL hideLoading;
@property (nonatomic, strong) UIImageView* iv;
@property (nonatomic , assign)BOOL isAddTag;
@property (nonatomic , assign)BOOL isVideo;
@property (nonatomic , assign)BOOL isFromFeatureonNow;
@property (nonatomic, strong) NSSet *toucheEvents;
@property (nonatomic, strong) UIImageView *imageView;

-(void)loadImageFromURL:(NSURL*)url;
-(void)loadImageFromURL:(NSURL*)url isBlur:(BOOL)isToBlur;
-(void)setDelegate:(id)aDelegate operation:(SEL)anOperation;
+(void)clearCache;
- (void)setImageForCurrentView;
- (void)setImageForCurrentView:(UIImage*)image;
@end
