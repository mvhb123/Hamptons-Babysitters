//
//  MLKMenuPopover.m
//  MLKMenuPopover
//
//  Created by NagaMalleswar on 20/11/14.
//  Copyright (c) 2014 NagaMalleswar. All rights reserved.
//

#import "MLKMenuPopover.h"
#import <QuartzCore/QuartzCore.h>

#define RGBA(a, b, c, d) [UIColor colorWithRed:(a / 255.0f) green:(b / 255.0f) blue:(c / 255.0f) alpha:d]

#define MENU_ITEM_HEIGHT        35
#define FONT_SIZE               15
#define CELL_IDENTIGIER         @"MenuPopoverCell"
#define MENU_TABLE_VIEW_FRAME   CGRectMake(0, 0, frame.size.width, frame.size.height)
#define SEPERATOR_LINE_RECT     CGRectMake(0, MENU_ITEM_HEIGHT - 1, self.frame.size.width, 1)
#define MENU_POINTER_RECT       CGRectMake(0, frame.origin.y, 23, 11)

#define CONTAINER_BG_COLOR      RGBA(0, 0, 0, 0.1f)

#define ZERO                    0.0f
#define ONE                     1.0f
#define ANIMATION_DURATION      0.5f

#define MENU_POINTER_TAG        1011
#define MENU_TABLE_VIEW_TAG     1012

#define LANDSCAPE_WIDTH_PADDING 50

@interface MLKMenuPopover ()

@property(nonatomic,retain) NSArray *menuItems;
@property(nonatomic,retain) UIButton *containerButton;
@property(nonatomic,assign)BOOL isSettingOpened;
- (void)hide;
- (void)addSeparatorImageToCell:(UITableViewCell *)cell;

@end

@implementation MLKMenuPopover

@synthesize menuPopoverDelegate;
@synthesize menuItems;
@synthesize containerButton;

- (id)initWithFrame:(CGRect)frame menuItems:(NSArray *)aMenuItems
{
    self = [super initWithFrame:frame];
    
    if (self)
    {
        self.menuItems = aMenuItems;
        
        // Adding Container Button which will take care of hiding menu when user taps outside of menu area
        self.containerButton = [[UIButton alloc] init];
        [self.containerButton setBackgroundColor:CONTAINER_BG_COLOR];
        [self.containerButton addTarget:self action:@selector(dismissMenuPopover) forControlEvents:UIControlEventTouchUpInside];
        [self.containerButton setAutoresizingMask:UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleBottomMargin];
        
        // Adding Menu Options Pointer
        //DDLogInfo(@"frm %@",NSStringFromCGRect(frame));
        AppDelegate *appDelegate=kAppDelegate;
        UIImageView *menuPointerView = [[UIImageView alloc] initWithFrame:CGRectMake(appDelegate.window.bounds.size.width-40, frame.origin.y, 23, 11)];
        menuPointerView.image = [UIImage imageNamed:@"options_pointer"];
        menuPointerView.tag = MENU_POINTER_TAG;
        [self.containerButton addSubview:menuPointerView];
        //        menuPointerView.layer.borderWidth=1.0;
        //        menuPointerView.layer.borderColor=kColorAppGreen.CGColor;
        
        // Adding menu Items table
        UITableView *menuItemsTableView = [[UITableView alloc] initWithFrame:CGRectMake(0, 11, frame.size.width, frame.size.height)];
        menuItemsTableView.separatorInset=UIEdgeInsetsZero;
        menuItemsTableView.dataSource = self;
        menuItemsTableView.delegate = self;
        menuItemsTableView.separatorStyle = UITableViewCellSeparatorStyleNone;
        menuItemsTableView.scrollEnabled = NO;
        menuItemsTableView.backgroundColor = kColorWhite;
        menuItemsTableView.tag = MENU_TABLE_VIEW_TAG;
        
        // UIImageView *bgView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"Menu_PopOver_BG"]];
        //menuItemsTableView.backgroundView = bgView;
        
        [self addSubview:menuItemsTableView];
        
        [self.containerButton addSubview:self];
    }
    
    return self;
}

#pragma mark -
#pragma mark UITableViewDatasource

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return MENU_ITEM_HEIGHT;
}

- (NSInteger)tableView:(UITableView *)table numberOfRowsInSection:(NSInteger)section
{
    return [self.menuItems count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *cellIdentifier = CELL_IDENTIGIER;
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier];
    
    if (cell == nil)
    {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:cellIdentifier];
        [cell.textLabel setFont:[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize]];
        [cell.textLabel setTextColor:kLabelColor];
        [cell setSelectionStyle:UITableViewCellSelectionStyleNone];
        [cell setBackgroundColor:[UIColor clearColor]];
    }
    
    NSInteger numberOfRows = [tableView numberOfRowsInSection:indexPath.section];
    if( [tableView numberOfRowsInSection:indexPath.section] > ONE && !(indexPath.row == numberOfRows - 1) )
    {
        [self addSeparatorImageToCell:cell];
    }
    cell.textLabel.font=kSystemFont;
    cell.textLabel.text = [self.menuItems objectAtIndex:indexPath.row];
//    if (indexPath.row==1) {
//        UISwitch *sw_notification=[[UISwitch alloc]init];
//        [sw_notification setOn:[[ApplicationManager getInstance].sitterInfo.appNotificationSetting boolValue]];//set default or updated value of app notification setting
//        [sw_notification addTarget:self action:@selector(onClickedNotoficationSwitch:) forControlEvents:UIControlEventValueChanged];
//        cell.accessoryView=sw_notification;
//    }
    
    return cell;
}

#pragma mark -
#pragma mark UITableViewDelegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    [self hide];
    [self.menuPopoverDelegate menuPopover:self didSelectMenuItemAtIndex:indexPath.row];
}
-(void)onClickedNotoficationSwitch:(id)sender{
    [self.menuPopoverDelegate setNotificationSettingForApp:sender];
}
-(void)updateMenuState:(id)sender{
    [self.menuPopoverDelegate updateMenuState:sender];
}
#pragma mark -
#pragma mark Actions

- (void)dismissMenuPopover
{
    [self hide];
    [self.menuPopoverDelegate updateMenuState:nil];
}

- (void)showInView:(UIView *)view
{
    if (self.isSettingOpened==false)
    {
        self.containerButton.alpha = ZERO;
        self.containerButton.frame = view.bounds;
        [view addSubview:self.containerButton];
        [UIView animateWithDuration:ANIMATION_DURATION
                         animations:^{
                             self.containerButton.alpha = ONE;
                         }
                         completion:^(BOOL finished) {}];
        self.isSettingOpened=true;
    }else{
        //
        [self hide];
        self.isSettingOpened=false;
    }
}

- (void)hide
{
    [UIView animateWithDuration:ANIMATION_DURATION
                     animations:^{
                         self.containerButton.alpha = ZERO;
                     }
                     completion:^(BOOL finished) {
                         [self.containerButton removeFromSuperview];
                     }];
}

#pragma mark -
#pragma mark Separator Methods

- (void)addSeparatorImageToCell:(UITableViewCell *)cell
{
    UIImageView *separatorImageView = [[UIImageView alloc] initWithFrame:SEPERATOR_LINE_RECT];
    [separatorImageView setImage:[self imageWithColor:kColorGrayLight]];
    separatorImageView.opaque = YES;
    [cell.contentView addSubview:separatorImageView];
}
-(UIImage *)imageWithColor:(UIColor *)color
{
    CGRect rect = CGRectMake(0.0f, 0.0f, 0.5f, 0.5f);
    UIGraphicsBeginImageContext(rect.size);
    CGContextRef context = UIGraphicsGetCurrentContext();
    
    CGContextSetFillColorWithColor(context, [color CGColor]);
    CGContextFillRect(context, rect);
    
    UIImage *image = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    return image;
}

#pragma mark -
#pragma mark Orientation Methods

- (void)layoutUIForInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    BOOL landscape = (interfaceOrientation == UIInterfaceOrientationLandscapeLeft || interfaceOrientation == UIInterfaceOrientationLandscapeRight);
    
    UIImageView *menuPointerView = (UIImageView *)[self.containerButton viewWithTag:MENU_POINTER_TAG];
    UITableView *menuItemsTableView = (UITableView *)[self.containerButton viewWithTag:MENU_TABLE_VIEW_TAG];
    
    if( landscape )
    {
        menuPointerView.frame = CGRectMake(menuPointerView.frame.origin.x + LANDSCAPE_WIDTH_PADDING, menuPointerView.frame.origin.y, menuPointerView.frame.size.width, menuPointerView.frame.size.height);
        
        menuItemsTableView.frame = CGRectMake(menuItemsTableView.frame.origin.x + LANDSCAPE_WIDTH_PADDING, menuItemsTableView.frame.origin.y, menuItemsTableView.frame.size.width, menuItemsTableView.frame.size.height);
    }
    else
    {
        menuPointerView.frame = CGRectMake(menuPointerView.frame.origin.x - LANDSCAPE_WIDTH_PADDING, menuPointerView.frame.origin.y, menuPointerView.frame.size.width, menuPointerView.frame.size.height);
        
        menuItemsTableView.frame = CGRectMake(menuItemsTableView.frame.origin.x - LANDSCAPE_WIDTH_PADDING, menuItemsTableView.frame.origin.y, menuItemsTableView.frame.size.width, menuItemsTableView.frame.size.height);
    }
}

@end

// Copyright belongs to original author
// http://code4app.net (en) http://code4app.com (cn)
// From the most professional code share website: Code4App.net